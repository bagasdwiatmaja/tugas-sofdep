<html>
<body>

<h1>Penilaian Sit Up</h1>
<form method="post">
Umur
<input type="text" name="umur" size="10">
<br><br>
Jenis Kelamin
<input type="radio" name="jenis_kelamin" value="Laki-Laki">
Laki-laki
<input type="radio" name="jenis_kelamin" value="Perempuan">
Perempuan
<br><br>
Jumlah Sit Up
<input type="text" name="situp" size="10">
<br><br>
<input type="submit" name="submit" value="Submit">
</form>

<?php

if (isset($_POST['submit']))
{
    $umur = $_POST['umur'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $situp = $_POST['situp'];

    $kategori = "";

    // Umur 9 tahun
    if ($umur == 9 && $jenis_kelamin == "Laki-Laki")
    {
        if ($situp <= 15)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 26)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 37)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 47)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }

    else if ($umur == 9 && $jenis_kelamin == "Perempuan")
    {
        if ($situp <= 14)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 24)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 34)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 44)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }

    // Umur 10 tahun
    else if ($umur == 10 && $jenis_kelamin == "Laki-Laki")
    {
        if ($situp <= 16)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 27)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 39)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 49)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }

    else if ($umur == 10 && $jenis_kelamin == "Perempuan")
    {
        if ($situp <= 15)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 25)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 37)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 46)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }

    // Umur 11 tahun
    else if ($umur == 11 && $jenis_kelamin == "Laki-Laki")
    {
        if ($situp <= 17)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 29)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 40)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 50)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }

    else if ($umur == 11 && $jenis_kelamin == "Perempuan")
    {
        if ($situp <= 19)
        {
            $kategori = "Sangat Rendah";
        }
        else if ($situp <= 30)
        {
            $kategori = "Rendah";
        }
        else if ($situp <= 40)
        {
            $kategori = "Cukup";
        }
        else if ($situp <= 51)
        {
            $kategori = "Baik";
        }
        else
        {
            $kategori = "Baik Sekali";
        }
    }
    else
    {
        $kategori = "Data belum memiliki penilaian";
    }

    echo "<br><br>";
    echo "Umur : $umur";
    echo "<br>";
    echo "Jenis Kelamin : $jenis_kelamin";
    echo "<br>";
    echo "Jumlah Sit Up : $situp";
    echo "<br>";
    echo "Kategori : $kategori";
}
?>

</body>
</html>