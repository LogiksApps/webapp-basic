# Service CMD

API Source Manager

System similar to remote api calls based on certain rules. It is a singular point of any remote api invokations.

* callReference : can be page:test, module:helloWorld, something for logging against what the script was running

### DB Table
> log_scmds


### Usage
loadModuleLib("serviceCmd","api");
runServiceCmd(callReference,cmdID,[]);


### Use Cases
+ Manual/Developer/Module->serviceCmd
+ bizRules->serviceCmd
+ automator->serviceCmd
