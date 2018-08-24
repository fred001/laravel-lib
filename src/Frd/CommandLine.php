<?php
   namespace Frd;

/* 
 *  实现机制:
 *    1. 解析预定义命令， 拆分成  cmd, params, options 三部分
 *    2. 解析时，先用 cmd 去匹配 输入， 若都满足，则认为是这个命令（所以长的命令应当放在前面）
 *    3. 剩下部分 首先获取关键词赋值，再对剩下进行次序赋值
 *    4. 最后检验命令必要的参数是否都存在，是则成功匹配，否则抛出异常，指示什么参数不满足
 *
 *    5. 若没有命令匹配，返回 false
 */

   class CommandLine
   {
      public $cmd_lines=[];

      function __construct($lines)
      {
         $this->cmd_lines=$this->parseSetting($lines);
      }

      function split($string)
      {
         $cols=explode(" ",$string);

         $params=[];

         foreach($cols as $col)
         {
            if(trim($col) )
            {
               $params[]=trim($col);
            }
         }

         if(count($params)  == 0) return false;

         return $params;
      }

      protected function parseSetting($lines)
      {
         $cmd_lines=[];
         foreach($lines as $line)
         {
            $cols=$this->split($line);

            $cmds=[];
            $params=[];

            foreach($cols as $col)
            {
               if($col[0] == ':')
               {
                  //if is cmd
                  //if is cmd=value

                  $match=[];
                  $pattern='/:(\w*)=(\w*)/';
                  $ret=preg_match_all($pattern, $col, $match);

                  if($ret)
                  {
                     $params[$match[1][0]]= $match[2][0];
                  }

                  if($ret == false)
                  {
                     $pattern='/:(\w*)/';
                     $ret=preg_match_all($pattern, $col, $match);
                     if($ret)
                     {
                        $params[$match[1][0]]=null;
                     }
                  }

                  if($ret == false)
                  {
                     //throw new Exception("unknown format");
                  }
               }
               else
               {
                  $cmds[]=$col;
               }
            }


            $cmd_lines[]=[
                'cmd'=>$cmds,
                'params'=>$params,
                'options'=>[],
            ];

         }

         return $cmd_lines;
      }

      function parse($string)
      {
         $cols=$this->split($string);

         //print_r($cols);exit();

         foreach($this->cmd_lines as $line)
         {
            $cmds=$line['cmd'];
            $params=$line['params'];

            if(count($cols) >= count($cmds)  )
            {
               $i=0;
               $match=True;

               for($i=0; $i < count($cmds); $i++)
               {
                  if($cmds[$i] != $cols[$i])
                  {
                     #print(cmds[i] == cols[i])
                     #print("False");
                     $match=False;

                     break;
                  }
               }

               if($match)
               {
                  //fill params
                  //$line: setting
                  //$cols  input string cols

                  $pos=count($cmds);

                  for($i=0; $i<$pos;$i++)
                  {
                     array_shift($cols);
                  }


                  for($i=0;$i< count($cols); $i++)
                  {
                     $match=[];
                     $pattern='/(\w*)=(\w*)/';
                     $ret=preg_match_all($pattern, $cols[$i], $match);

                     //var_dump($cols[$i]);
                     //var_dump($ret);

                     if($ret)
                     {
                        $params[$match[1][0]]= $match[2][0];
                        //var_dump($parms);

                        unset($cols[$i]);
                     }
                  }

                  $i=0;
                  foreach($params as $k=>$v)
                  {
                     if(isset($cols[$i]))
                     {
                        $params[$k]= $cols[$i];
                     }

                     $i+=1;
                  }

                  //check result ,every 
                  foreach($params as $k=>$v)
                  {
                     if($v === null)
                     {
                        throw new \Exception("miss param:$k");
                     }
                  }


                  return [$cmds,$params];
               }
            }
         }

         return false;
      }
   }

   /*
    *
    * talk choose :role_id
    * talk :role_id
    * talk end   :role_id
    * talk end  -v  :role_id
    */
