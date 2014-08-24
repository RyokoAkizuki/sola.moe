#include <phpcpp.h>
#include <iostream>
#include "Interface.h"

/**
 *  Example function that shows how to generate output
 */
void helloWorld()
{
    // the C++ equivalent of the echo() function
    Php::out << "Hello world." << std::endl;
}

/**
 *  tell the compiler that the get_module is a pure C function
 */
extern "C" {
    
    /**
     *  Function that is called by PHP right after the PHP process
     *  has started, and that returns an address of an internal PHP
     *  strucure with all the details and features of your extension
     *
     *  @return void*   a pointer to an address that is understood by PHP
     */
    PHPCPP_EXPORT void *get_module() 
    {
        // static(!) Php::Extension object that should stay in memory
        // for the entire duration of the process (that's why it's static)
        static Php::Extension extension("moenosola", "1.0");
        
        extension.add("moe_hello",              helloWorld);
        extension.add("moe_createSession",      createSession);
        extension.add("moe_seekPair",           seekPair);
        extension.add("moe_getSessionPair",     getSessionPair);
        extension.add("moe_sendMessageToPair",  sendMessageToPair);
        extension.add("moe_peekMessage",        peekMessage);
        extension.add("moe_getPairRole",        getPairRole);
        extension.add("moe_getSessionCount",    getSessionCount);
        extension.add("moe_getSessionInfo",     getSessionInfo);
        extension.add("moe_closeSession",       closeSession);

        // return the extension
        return extension;
    }
}
