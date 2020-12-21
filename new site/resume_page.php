<?php
require(__DIR__.'/../config/resume_conn.php');
require(__DIR__.'/mpdf/autoload.php');

$html="";
$con=mysqli_connect($host,$user,$pass,$dbname) or die('Could not generate the document as connection to database was unsuccessful.');
$education=mysqli_query($con,'select * from education order by id desc limit 2;');
$work=mysqli_query($con,"select * FROM work where post not like '%member%' order by id desc limit 4;");
$skills=mysqli_query($con,'select * FROM skills order by type;');
$certifications=mysqli_query($con,'select * FROM certifications order by id desc limit 4;');
$additional=mysqli_query($con,'select * FROM additional;');


$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'fontDir' => array_merge($fontDirs, [
        __DIR__ . '/fonts/calibri',
    ]),
    'fontdata' => $fontData + [
        'calibri' => [
            'R' => 'calibri.ttf',
            'I' => 'calibrii.ttf',
        ]
    ],
    'default_font' => 'calibri'
]);

$html.="<style>
table{
  width: 100%;
  margin:0;
  padding:0;
}

body{
  font-size:13px;
}

tr{
  border:1px solid black;
}

th,td{
  padding:0;
  margin:0;
  vertical-align:top;
  text-align:left;
  width:50%;
  text-align:justify;
}

td{
  margin-top:100px;
}

.left{
  font-weight:bold;
  font-size:15px;
}

a{
  text-decoration:none;
  font-style:oblique;
  color:#7F0DD1;
}
</style><body>
<table style='border-collapse: separate;border-spacing: 0 30px;'>
  <tr>
    <th style='font-size:18px;color:#0D6ED1;text-decoration:underline;'>Devjyot Singh Sidhu</th>
    <th style='text-align:right;'><a href='mailto:devjyotsinhsidhu@gmail.com' style='color:#499E60;'>devjyotsinhsidhu@gmail.com</a><br>
      <a href='tel:+919818911553' style='color:#D25EBB;'>+919818911553</a>
    </th>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Goal in Life</td>
    <td>I’m a tech enthusiast who has the dream to
        explore the very limits of Machine Learning and
        Artificial Intelligence and achieve something
        great for the society at large through these technologies.
         My goal is also to promote freelance software developers
        and support them and their talents at
        extremely low or no costs.</td>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Education</td>
    <td>";
    $num=mysqli_num_rows($education);
    while($row=mysqli_fetch_assoc($education)){
      $num--;
      $html.='<b>'.$row['institute']." '".substr($row['graduation_year'],2).'</b><br><u>'.$row['degree'].'</u><br>';
      if($row['score']<0){
        $html.='Currently Enrolled';
      }
      else{
        $html.=$row['score'].' ('.$row['mode'].')';
      }
      if($num>=1){
        $html.='<br><br>';
      }
    }
    $html.="</td>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Work Experience<br>and Positions of Responsibilities</td>
    <td>";
    $num=mysqli_num_rows($work);
    while($row=mysqli_fetch_assoc($work)){
      $num--;
      $html.='<b>'.$row['post'].' at '.$row['organisation'].'</b><br><u>'.$row['start_month'].'-';
      if($row['end_month']==-1){
        $html.='present</u>';
      }
      else{
        $html.=$row['end_month'].'</u>';
      }
      if($row['certificate']!=null){
        $html.="<br><a href='".$row['certificate']."'>Reference Certificate</a>";
      }
      if($num>=1){
        $html.='<br><br>';
      }
    }
    $html.="</td>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Certifications</td>
    <td>";
    $num=mysqli_num_rows($certifications);
    while($row=mysqli_fetch_assoc($certifications)){
      $num--;
      $html.='<b>'.$row['platform'].' and '.$row['issuer'].'</b><br>'.$row['title'].'<br><a href="'.$row['link'].'">Reference Certificate</a>';
      if($num>=1){
        $html.='<br><br>';
      }
    }
    $html.="</td>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Skils</td>
    <td>
      <table>";
      $num=mysqli_num_rows($skills);
      while($row=mysqli_fetch_assoc($skills)){
        $num--;
        if($num%2!=0){
          $html.="<tr><td style='padding-right:30px;text-align:left;'><b>".$row['name'].'</b><br>'.$row['rating'].'</td>';
        }
        else{
          $html.="<td style='text-align:left;'><b>".$row['name'].'</b><br>'.$row['rating'].'</td></tr>';
        }
        if($num>=1){
          $html.='<br><br>';
        }
      }
      $html.="</table>
    </td>
  </tr>
  <br><br>
  <tr>
    <td class='left'>Miscellaneous Information</td>
    <td>";
    $num=mysqli_num_rows($additional);
    while($row=mysqli_fetch_assoc($additional)){
      $num--;
      if($row['link']!=null){
        $html.='<b><a href="'.$row['link'].'">'.$row['title'].'</a></b><br>'.$row['description'];
      }
      else{
        $html.='<b>'.$row['title'].'</b><br>'.$row['description'];
      }
      if($num>=1){
        $html.='<br><br>';
      }
    }
    $html.="</td>
  </tr>
</table></body>";

$mpdf=new Mpdf\Mpdf();
$mpdf->setTitle('Devjyot Singh Sidhu_Resume');
$mpdf->writeHTML($html);
$file='Devjyot Singh Sidhu_Resume.pdf';
$mpdf->output($file,'I');
?>
