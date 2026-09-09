Add-Type -AssemblyName System.Drawing
$bitmap = New-Object System.Drawing.Bitmap 64,64
$graphics = [System.Drawing.Graphics]::FromImage($bitmap)
$graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$graphics.Clear([System.Drawing.ColorTranslator]::FromHtml('#241b14'))
$brush = New-Object System.Drawing.SolidBrush ([System.Drawing.ColorTranslator]::FromHtml('#d89624'))
$points = [System.Drawing.Point[]]@(
    [System.Drawing.Point]::new(14,16), [System.Drawing.Point]::new(25,16),
    [System.Drawing.Point]::new(32,42), [System.Drawing.Point]::new(39,16),
    [System.Drawing.Point]::new(50,16), [System.Drawing.Point]::new(37,50),
    [System.Drawing.Point]::new(27,50)
)
$graphics.FillPolygon($brush, $points)
$png = New-Object System.IO.MemoryStream
$bitmap.Save($png, [System.Drawing.Imaging.ImageFormat]::Png)
$destination = Join-Path (Split-Path $PSScriptRoot -Parent) 'favicon.ico'
$stream = [System.IO.File]::Create($destination)
$writer = New-Object System.IO.BinaryWriter $stream
$writer.Write([UInt16]0)
$writer.Write([UInt16]1)
$writer.Write([UInt16]1)
$writer.Write([byte]64)
$writer.Write([byte]64)
$writer.Write([byte]0)
$writer.Write([byte]0)
$writer.Write([UInt16]1)
$writer.Write([UInt16]32)
$writer.Write([UInt32]$png.Length)
$writer.Write([UInt32]22)
$writer.Write($png.ToArray())
$writer.Dispose()
$png.Dispose()
$brush.Dispose()
$graphics.Dispose()
$bitmap.Dispose()
