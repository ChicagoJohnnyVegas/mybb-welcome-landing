[CmdletBinding()]
param(
    [string] $OutputRoot = "dist",
    [string] $PackageName = "WelcomeLanding",
    [switch] $NoZip
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Write-Step {
    param([Parameter(Mandatory = $true)][string] $Message)
    Write-Host "==> $Message"
}

function Assert-NoReleaseLink {
    param([Parameter(Mandatory = $true)][string] $Path)

    # Walk explicitly: never recurse through a junction while checking it.
    $item = Get-Item -LiteralPath $Path -Force -ErrorAction SilentlyContinue
    if ($null -eq $item) { return }
    if ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) {
        throw "Release paths must not contain links or junctions: $Path"
    }
    if ($item.PSIsContainer) {
        foreach ($child in Get-ChildItem -LiteralPath $Path -Force) {
            Assert-NoReleaseLink -Path $child.FullName
        }
    }
}

function Get-SafeReleasePaths {
    param(
        [Parameter(Mandatory = $true)][string] $RepoRoot,
        [AllowEmptyString()][string] $OutputRoot,
        [AllowEmptyString()][string] $PackageName
    )

    # A deliberately small Windows-safe name grammar excludes wildcards, ADS,
    # device names and trailing-dot/space aliases before paths are constructed.
    $segments = @($OutputRoot -split '[\\/]')
    foreach ($name in @($segments) + @($PackageName)) {
        if ($name -notmatch '\A[A-Za-z0-9][A-Za-z0-9_.-]*\z' -or
            $name.EndsWith('.') -or
            $name -match '\A(CON|PRN|AUX|NUL|COM[0-9]|LPT[0-9])(?:\.|$)') {
            throw "Unsafe release path component: '$name'"
        }
    }
    if ($segments[0] -ine 'dist') {
        throw 'OutputRoot must be dist or a relative subdirectory of dist.'
    }

    $repoPath = [IO.Path]::GetFullPath($RepoRoot).TrimEnd('\', '/')
    $distPath = Join-Path $repoPath 'dist'
    $outputPath = [IO.Path]::GetFullPath((Join-Path $repoPath ($segments -join '\')))
    $packagePath = [IO.Path]::GetFullPath((Join-Path $outputPath $PackageName))
    $zipPath = [IO.Path]::GetFullPath((Join-Path $outputPath "$PackageName.zip"))
    $markerPath = Join-Path $outputPath ".$PackageName.release-owner"
    foreach ($path in @($packagePath, $zipPath, $markerPath)) {
        if (-not $path.StartsWith($distPath + '\', [StringComparison]::OrdinalIgnoreCase) -or
            -not $path.StartsWith($outputPath + '\', [StringComparison]::OrdinalIgnoreCase)) {
            throw "Release target is outside the output directory: $path"
        }
    }

    # Check ancestors individually, including ancestors of the checkout.
    $ancestor = $outputPath
    while ($ancestor) {
        $item = Get-Item -LiteralPath $ancestor -Force -ErrorAction SilentlyContinue
        if ($null -ne $item -and
            (($item.Attributes -band [IO.FileAttributes]::ReparsePoint) -or -not $item.PSIsContainer)) {
            throw "Release output ancestor is not an ordinary directory: $ancestor"
        }
        $ancestor = Split-Path -Parent $ancestor
    }
    foreach ($path in @($packagePath, $zipPath, $markerPath)) {
        Assert-NoReleaseLink -Path $path
    }
    if ((Test-Path -LiteralPath $packagePath) -and -not (Test-Path -LiteralPath $packagePath -PathType Container)) {
        throw "Package target is not a directory: $packagePath"
    }
    foreach ($path in @($zipPath, $markerPath)) {
        if ((Test-Path -LiteralPath $path) -and -not (Test-Path -LiteralPath $path -PathType Leaf)) {
            throw "Release file target is not a file: $path"
        }
    }

    $owner = "WelcomeLanding release output v1`n$packagePath"
    if (Test-Path -LiteralPath $markerPath) {
        if ([IO.File]::ReadAllText($markerPath) -cne $owner) {
            throw "Release ownership marker does not match: $markerPath"
        }
    }
    elseif ((Test-Path -LiteralPath $packagePath) -or (Test-Path -LiteralPath $zipPath)) {
        throw "Refusing to replace unmarked output. Choose a fresh OutputRoot under dist or preserve and move the old output manually: $packagePath"
    }

    return [pscustomobject]@{
        OutputRoot = $outputPath
        PackageRoot = $packagePath
        ZipPath = $zipPath
        MarkerPath = $markerPath
        Owner = $owner
    }
}

function Copy-RequiredFile {
    param(
        [Parameter(Mandatory = $true)][string] $Source,
        [Parameter(Mandatory = $true)][string] $Destination
    )

    if (-not (Test-Path -LiteralPath $Source -PathType Leaf)) {
        throw "Required file missing: $Source"
    }

    $destinationDirectory = Split-Path -Parent $Destination
    if (-not [string]::IsNullOrWhiteSpace($destinationDirectory)) {
        New-Item -ItemType Directory -Force -Path $destinationDirectory | Out-Null
    }

    Copy-Item -LiteralPath $Source -Destination $Destination -Force
}

function Copy-RequiredDirectory {
    param(
        [Parameter(Mandatory = $true)][string] $Source,
        [Parameter(Mandatory = $true)][string] $Destination
    )

    if (-not (Test-Path -LiteralPath $Source -PathType Container)) {
        throw "Required directory missing: $Source"
    }

    New-Item -ItemType Directory -Force -Path $Destination | Out-Null
    Get-ChildItem -LiteralPath $Source -Force | ForEach-Object {
        Copy-Item -LiteralPath $_.FullName -Destination $Destination -Recurse -Force
    }
}

function Assert-NoForbiddenPackagePath {
    param([Parameter(Mandatory = $true)][string] $PackageRoot)

    $forbiddenNames = @('.git', 'Docs', 'Tools')
    foreach ($name in $forbiddenNames) {
        $matches = Get-ChildItem -LiteralPath $PackageRoot -Recurse -Force -Directory |
            Where-Object { $_.Name -ieq $name }
        if ($matches) {
            $paths = ($matches | ForEach-Object { $_.FullName }) -join "`n"
            throw "Forbidden directory found in release package:`n$paths"
        }
    }

    $deprecatedMatches = Get-ChildItem -LiteralPath $PackageRoot -Recurse -Force |
        Where-Object { $_.FullName -match '\\deprecated(\\|$)' }
    if ($deprecatedMatches) {
        $paths = ($deprecatedMatches | ForEach-Object { $_.FullName }) -join "`n"
        throw "Deprecated content found in release package:`n$paths"
    }
}

function Assert-NoPrivateStrings {
    param([Parameter(Mandatory = $true)][string] $PackageRoot)

    $patterns = @(
        'MMIV',
        'mmivbaby',
        'Meet Me In Vegas',
        'Fuhged',
        'Daddy-o',
        'LV1',
        'LV\*',
        'mmiv_welcome',
        'mmiv_landing',
        'Reference Implementation'
    )

    $extensions = @('.md', '.txt', '.php', '.css', '.js', '.json', '.html', '.htm')
    $files = Get-ChildItem -LiteralPath $PackageRoot -Recurse -File -Force |
        Where-Object { $extensions -contains $_.Extension.ToLowerInvariant() }

    $hits = New-Object System.Collections.Generic.List[string]
    foreach ($file in $files) {
        $content = [System.IO.File]::ReadAllText($file.FullName)
        foreach ($pattern in $patterns) {
            if ($content -match $pattern) {
                [void]$hits.Add("$($file.FullName): $pattern")
            }
        }
    }

    if ($hits.Count -gt 0) {
        throw "Private or deprecated strings found in release package:`n$($hits -join "`n")"
    }
}

try {
    $repoRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
    Set-Location -LiteralPath $repoRoot

    $paths = Get-SafeReleasePaths -RepoRoot $repoRoot -OutputRoot $OutputRoot -PackageName $PackageName
    $outputRootPath = $paths.OutputRoot
    $packageRoot = $paths.PackageRoot
    $uploadRoot = Join-Path $packageRoot 'Upload'
    $zipPath = $paths.ZipPath

    # Source links must not copy outside files into the package either.
    foreach ($source in @('README.md', 'CHANGELOG.md', 'UPGRADE.md', 'LICENSE', 'THIRD_PARTY_NOTICES.md', 'inc', 'landing')) {
        Assert-NoReleaseLink -Path (Join-Path $repoRoot $source)
    }

    Write-Step "Preparing output folder"
    New-Item -ItemType Directory -Force -Path $outputRootPath | Out-Null
    [IO.File]::WriteAllText($paths.MarkerPath, $paths.Owner)
    if (Test-Path -LiteralPath $packageRoot) {
        Remove-Item -LiteralPath $packageRoot -Recurse -Force
    }
    if (Test-Path -LiteralPath $zipPath) {
        Remove-Item -LiteralPath $zipPath -Force
    }

    Write-Step "Copying package root documentation"
    Copy-RequiredFile -Source 'README.md' -Destination (Join-Path $packageRoot 'README.md')
    Copy-RequiredFile -Source 'CHANGELOG.md' -Destination (Join-Path $packageRoot 'CHANGELOG.md')
    Copy-RequiredFile -Source 'UPGRADE.md' -Destination (Join-Path $packageRoot 'UPGRADE.md')
    Copy-RequiredFile -Source 'LICENSE' -Destination (Join-Path $packageRoot 'LICENSE')
    Copy-RequiredFile -Source 'THIRD_PARTY_NOTICES.md' -Destination (Join-Path $packageRoot 'THIRD_PARTY_NOTICES.md')

    Write-Step "Copying upload payload"
    Copy-RequiredFile -Source 'inc\plugins\welcome_landing.php' -Destination (Join-Path $uploadRoot 'inc\plugins\welcome_landing.php')
    Copy-RequiredFile -Source 'inc\languages\english\welcome_landing.lang.php' -Destination (Join-Path $uploadRoot 'inc\languages\english\welcome_landing.lang.php')
    Copy-RequiredFile -Source 'inc\languages\english\admin\welcome_landing.lang.php' -Destination (Join-Path $uploadRoot 'inc\languages\english\admin\welcome_landing.lang.php')
    Copy-RequiredDirectory -Source 'landing' -Destination (Join-Path $uploadRoot 'landing')

    Write-Step "Verifying release package"
    Assert-NoForbiddenPackagePath -PackageRoot $packageRoot
    Assert-NoPrivateStrings -PackageRoot $packageRoot

    if (-not $NoZip) {
        Write-Step "Creating ZIP archive"
        Compress-Archive -LiteralPath $packageRoot -DestinationPath $zipPath -Force
    }

    Write-Host ''
    Write-Host 'Release package build complete.'
    Write-Host "Package folder: $packageRoot"
    if (-not $NoZip) {
        Write-Host "Package ZIP:    $zipPath"
    }
}
catch {
    Write-Error $_
    exit 1
}
