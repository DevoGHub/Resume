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

$html.="<style>
table{
  width: 100%;
  margin:0;
  padding:0
}

body{
  font-size:13px;
}

th,td{
  padding:0
  margin:0;
  vertical-align:middle;
  text-align:left;
  width:50%;
}
</style><body>
<table>
  <tr>
    <th>Devjyot Singh Sidhu</th>
    <th>devjyotsinhsidhu@gmail.com<br>
      +919818911553
    </th>
  </tr>
  <br><br>
  <tr>
    <td>Goal in Life</td>
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
    <td>Education</td>
    <td>";
    $num=mysqli_num_rows($education);
    while($row=mysqli_fetch_assoc($education)){
      $num--;
      $html.=$row['institute']." '".substr($row['graduation_year'],2).'<br>'.$row['degree'].'<br>';
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
    <td>Work Experience<br>and Positions of Responsibilities</td>
    <td>";
    $num=mysqli_num_rows($work);
    while($row=mysqli_fetch_assoc($work)){
      $num--;
      $html.=$row['post'].' at '.$row['organisation'].'<br>'.$row['start_month'].'-';
      if($row['end_month']==-1){
        $html.='present';
      }
      else{
        $html.=$row['end_month'];
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
    <td>Certifications</td>
    <td>";
    $num=mysqli_num_rows($certifications);
    while($row=mysqli_fetch_assoc($certifications)){
      $num--;
      $html.=$row['platform'].' and '.$row['issuer'].'<br>'.$row['title'].'<br><a href="'.$row['link'].'">Reference Certificate</a>';
      if($num>=1){
        $html.='<br><br>';
      }
    }
    $html.="</td>
  </tr>
  <br><br>
  <tr>
    <td>Skils</td>
    <td>
      <table>";
      $num=mysqli_num_rows($skills);
      while($row=mysqli_fetch_assoc($skills)){
        $num--;
        if($num%2!=0){
          $html.="<tr><td>".$row['name'].'<br>'.$row['rating'].'</td>';
        }
        else{
          $html.="<td>".$row['name'].'<br>'.$row['rating'].'</td></tr>';
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
    <td>Miscellaneous Information</td>
    <td>";
    $num=mysqli_num_rows($additional);
    while($row=mysqli_fetch_assoc($additional)){
      $num--;
      if($row['link']!=null){
        $html.='<a href="'.$row['link'].'">'.$row['title'].'</a><br>'.$row['description'];
      }
      else{
        $html.=$row['title'].'<br>'.$row['description'];
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
$file='Devjyot Singh Sidhu_Resume';
$mpdf->output($file,'I');
?>
