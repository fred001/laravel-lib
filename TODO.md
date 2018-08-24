# Command Line

1. supprt command 
2. support  default value
  talk choose :role_id=1 :option
3. support short option (switch, should after command)
  talk choose -v :role_id=1 :option

  


talk choose :role_id :option
talk :role_id

[input]   talk 1 
[expect]  talk : role_id


[input]   talk choose 6
[expect]  talk choose :role_id

2 $str="hello :name";
  3
  4 $vars=[
  5     'name'=>'world',
  6     'age'=>11,
  7 ];
  8
  9 function str_template($str,$vars=[])
 10 {
 11     $pattern=[];
 12     $replace=[];
 13     foreach($vars as $k=>$v)
 14     {
 15         $pattern[]=":".$k;
 16         $replace[]=$v;
 17     }
 18
 19     return str_replace($pattern,$vars,$str);
 20 }
 21
 22
 23 echo str_template($str,$vars);
~
~
