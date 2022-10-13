# Sys Scripts

System Scripts module enables custom coding interfacing any where in the system. It helps code injection into predefiend modules

* callReference : can be page:test, module:helloWorld, something for logging against what the script was running

### DB Table
> log_scripts


### Usage
loadModuleLib("sysScripts","api");
runSysScript(callReference,scriptID/scriptSlug,[]);


### Use Cases
+ Manual/Developer/Module->sysScripts
+ bizRules->sysScripts
+ automator->sysScripts