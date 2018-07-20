<?php
   namespace Frd;

   class CommandLine
   {
      protected $cmd_lines=[];

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


            $cmd_lines[]=[$cmds,$params];

         }

         return $cmd_lines;
      }

      function parse($string)
      {
         $cols=$this->split($string);

         foreach($this->cmd_lines as $line)
         {
            $cmds=$line[0];
            $params=$line[1];

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

                  $i+=1;
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

