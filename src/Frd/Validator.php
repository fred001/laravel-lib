<?php
/*
 * 仿照Laravel 验证器的用法
 * 实现一个非常简单的验证类
 *
 * 验证流程
 * 1. 获取所有值,通过allow_keys 过滤值
 * 2. 与默认值进行合并
 * 3. 应用前置过滤规则
 * 4. 进行规则校验
 *     required =  存在值，且不为  '' ,但是可以是   0
 *     int  必须为整数
 *     min:0  （整数用， 必须大于等于某数字）
 *     max:0  (整数用，必须大于等于某数字）
 */
/*
 * Example

 $validator=new Validator();

$data=[
    'name'=>'frd',
    'age'=>'11',
    'level'=>'',
];

$params=[
];
$rules=[
    'age'=>'int|min:1|max:100',
    'name'=>'required',
    'level'=>'required',
];

$pre_filter=[
    'age'=>'trim|toint',
];

$attrs=[
    'level'=>"等级",
];
$validator->valid($data,$params,$rules,$pre_filter,$messages=[],$attrs);


if($validator->fails())
{
    $error = $validator->getError();
    echo $error;
}
else
{
    $data=$validator->getData();

    var_dump($data);
}
 */



class Validator
{
    protected $failed=false;
    protected $errors=[];
    protected $attrs=[];
    protected $messages=[
        'required'=>'%s 字段未提供',
        'min'=>'%s 不能小于 %s',
        'max'=>'%s 不能大于 %s',
    ];

    protected $data=[];

    function getData()
    {
        return $this->data;
    }

    function fails()
    {
        return $this->failed;
    }

    function error($name,$rule,$value='')
    {
        $this->failed=true;
        $this->errors[]=$this->getMessage($rule,$name,$value);
    }

    function getError()
    {
        return $this->errors[0];
    }

    function getMessage($rule,$name,$value)
    {
        $name=$this->getAttr($name);

        if(in_array($rule ,['required']))
        {
            $message=sprintf($this->messages[$rule],$name);
        }
        else
        {
            $message=sprintf($this->messages[$rule],$name,$value);
        }

        return $message;
    }

    function getAttr($name)
    {
        if(! empty($this->attrs[$name])) 
        {
            return $this->attrs[$name];
        }
        else
        {
            return $name;
        }
    }

    function valid($data,$allow_keys,$params=[],$rules=[],$pre_filters=[],$messages=[],$attrs=[])
    {
        $this->attrs=$attrs;

        foreach($messages as $name=>$messsage)
        {
            $this->messages[$name]=$messages;
        }

        //
        $filtered_data=[];
        foreach($allow_keys as $key)
        {
            if(isset($data[$key]))
            {
                $filtered_data[$key]=$data[$key];
            }
        }

        $data=array_merge($params,$filtered_data);

        foreach($pre_filters as $name=>$filter)
        {
            $filters=explode("|",$filter);

            //'age'=>'trim|toint',
            foreach($filters as $filter)
            {
                if($filter == "trim" && isset($data[$name]))
                {
                    $data[$name]=trim($data[$name]);
                }
                else if($filter == "toint" && isset($data[$name]))
                {
                    $data[$name]=intval($data[$name]);
                }
            }
        }

        /*
         这里是不需要的，因为原先没有allow_keys, 才导致必须排除某些参数的问题
        foreach($data as $k=>$v)
        {
            if($v === null)
            {
                unset($data[$k]);
            }
        }
         */


        foreach($rules as $name=>$value)
        {
            $cols=explode("|",$value);

            //required, min:1, max:10
            foreach($cols as $col)
            {
                //调整为一致的格式  k:v
                if(strpos($col,":") === false) $col.=":_"; 

                $v=explode(":",$col);

                if(
                    !isset($data[$name])
                    || $data[$name] === ''
                    || $data[$name] === false
                    || $data[$name] === []
                )
                {
                    $this->error($name,'required');
                    return false;
                }

                if($v[0] == "required" )
                {
                    //前面已经检测过
                }
                else if($v[0] == "min" )
                {
                    if($data[$name] < $v[1])
                    {
                        $this->error($name,'min',$v[1]);
                        return false;
                    }
                }
                else if($v[0] == "max" )
                {
                    if($data[$name] > $v[1])
                    {
                        $this->error($name,'max',$v[1]);
                        return false;
                    }
                }
            }
        }


        $this->data=$data;

        return $data;
    }
}
