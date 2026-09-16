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

    $outputRootPath = Join-Path $repoRoot $OutputRoot
    $packageRoot = Join-Path $outputRootPath $PackageName
    $uploadRoot = Join-Path $packageRoot 'Upload'
    $zipPath = Join-Path $outputRootPath "$PackageName.zip"

    Write-Step "Preparing output folder"
    New-Item -ItemType Directory -Force -Path $outputRootPath | Out-Null
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
