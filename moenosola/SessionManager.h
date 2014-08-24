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

#include <map>
#include <ctime>
#include <memory>
#include <mutex>
#include <deque>
#include <string>
#include <boost/bimap.hpp>
#include <boost/uuid/uuid.hpp>
#include <boost/uuid/uuid_generators.hpp>
#include <boost/uuid/uuid_io.hpp>

enum SEX
{
    FEMALE = 0,
    MALE = 1,
    INVALID = 2
};

class Session
{
protected:
    std::string             mId;
    std::string             mRole;
    SEX                     mSex;
    SEX                     mSeek;
    time_t                  mLastPing;
    std::deque<std::string> mMsgQueue;
    std::mutex              mMutex;
    bool                    mDisabled;

public:
    static std::string      InvalidSession;

public:
    Session(const std::string& role, SEX sex, SEX seek)
        : mId(to_string(boost::uuids::random_generator()()))
        ,mRole(role), mSex(sex), mSeek(seek), mLastPing(time(NULL))
        ,mDisabled(false)
    {}

    std::string getId()                 { return mId; }
    std::string getRole()               { return mRole; }
    SEX         getSelfSex()            { return mSex; }
    SEX         getSeekSex()            { return mSeek; }
    void        ping()                  { mLastPing = time(NULL); }
    bool        isExpired()             { return (time(NULL) - mLastPing) > 10; }
    bool        isDisabled()            { return mDisabled; }
    void        disable()               { mDisabled = true; }
    bool        isValid()               { return !isExpired() && !isDisabled(); }
    void        sendMessage(const std::string& message)
    {
        ping();
        std::unique_lock<std::mutex> lock(mMutex);
        mMsgQueue.push_back(message);
    }
    std::string peekMessage()
    {
        ping();
        std::unique_lock<std::mutex> lock(mMutex);
        if(mMsgQueue.empty())
        {
            return "";
        }
        else
        {
            auto msg = mMsgQueue.front();
            mMsgQueue.pop_front();
            return msg;
        }
    }
};

class SessionManager
{
protected:
    typedef boost::bimap<std::string, std::string>  PairMap;
    typedef PairMap::value_type                     Pair;

    std::map<std::string, std::shared_ptr<Session>> mSessions;
    PairMap                                         mPairs;
    std::mutex                                      mSessionMutex, mPairMutex;

public:
    virtual ~SessionManager() {}

    std::shared_ptr<Session>    createSession(const std::string& role, SEX sex, SEX seek);
    std::shared_ptr<Session>    findSession(const std::string& id);
    bool                        seekPair(const std::string& id);
    std::string                 getSessionPair(const std::string& id);
    void                        clearInvalidSessions();
    int64_t                     getSessionCount() const;
    void                        closeSession(const std::string& id);
};
