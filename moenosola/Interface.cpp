/*
 * Copyright 2014 Yukino Hayakawa<tennencoll@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

#include "SessionManager.h"
#include "Interface.h"

SessionManager gMgr;

SEX stringToSex(const std::string& str)
{
    if(str == "male")
    {
        return SEX::MALE;
    }
    else if(str == "female")
    {
        return SEX::FEMALE;
    }
    else
    {
        return SEX::INVALID;
    }
}

// int (string role, string sex, string seek)
Php::Value createSession(Php::Parameters &params)
{
    auto ptr = gMgr.createSession(params[0], stringToSex(params[1]), stringToSex(params[2]));
    return ptr->getId();
}

// bool (int sid)
Php::Value isSessionValid(Php::Parameters &params)
{
    return gMgr.findSession(params[0]) != nullptr;
}

// bool (int sid)
Php::Value seekPair(Php::Parameters &params)
{
    return gMgr.seekPair(params[0]);
}

// int (int sid)
Php::Value getSessionPair(Php::Parameters &params)
{
    return gMgr.getSessionPair(params[0]);
}

// bool (int sid, string msg)
Php::Value sendMessageToPair(Php::Parameters &params)
{
    auto ptr = gMgr.findSession(gMgr.getSessionPair(params[0]));
    if(!ptr)
    {
        return false;
    }
    ptr->sendMessage(params[1]);
    return true;
}

// string / bool (int sid)
Php::Value peekMessage(Php::Parameters &params)
{
    auto ptr = gMgr.findSession(params[0]);
    if(!ptr)
    {
        return false;
    }
    auto msg = ptr->peekMessage();
    if(msg.empty())
    {
        return false;
    }
    return msg;
}

// string / bool (int sid)
Php::Value getPairRole(Php::Parameters &params)
{
    auto ptr = gMgr.findSession(gMgr.getSessionPair(params[0]));
    if(!ptr)
    {
        return false;
    }
    return ptr->getRole();
}

Php::Value getSessionCount()
{
    return gMgr.getSessionCount();
}

// array (int sid)
Php::Value getSessionInfo(Php::Parameters &params)
{
    auto ptr = gMgr.findSession(params[0]);
    if(!ptr)
    {
        return false;
    }
    Php::Value r;
    r["id"] = ptr->getId();
    r["role"] = ptr->getRole();
    r["sex"] = ptr->getSelfSex();
    r["seek"] = ptr->getSeekSex();
    r["expired"] = ptr->expired();
    return r;
}

// void (int sid)
void closeSession(Php::Parameters &params)
{
    gMgr.closeSession(params[0]);
}
