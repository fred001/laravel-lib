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
