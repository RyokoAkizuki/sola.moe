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
#include <string>
#include <ctime>
#include <memory>
#include <mutex>
#include <atomic>
#include <deque>
#include <string>
#include <thread>
#include <chrono>
#include <thread>
#include <boost/bimap.hpp>

enum SEX
{
    FEMALE = 0,
    MALE = 1,
    INVALID = 2
};

class Session
{
protected:
    std::atomic<int64_t>    mId;
    std::string             mRole;
    std::atomic<SEX>        mSex;
    std::atomic<SEX>        mSeek;
    time_t                  mLastPing;
    std::deque<std::string> mMsgQueue;
    std::mutex              mMutex;
    bool                    mDisabled;

public:
    Session(int64_t id, const std::string& role, SEX sex, SEX seek)
        : mId(id), mRole(role), mSex(sex), mSeek(seek), mLastPing(time(NULL))
        ,mDisabled(false)
    {}

    int64_t     getId()                 { return mId; }
    std::string getRole()               { return mRole; }
    SEX         getSelfSex()            { return mSex; }
    SEX         getSeekSex()            { return mSeek; }
    void        ping()                  { mLastPing = time(NULL); }
    bool        expired()               { return (time(NULL) - mLastPing) > 60; }
    bool        isDisabled()            { return mDisabled; }
    void        disable()               { mDisabled = true; }
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
    typedef boost::bimap<int64_t, int64_t>          PairMap;
    typedef PairMap::value_type                     Pair;

    std::map<int64_t, std::shared_ptr<Session>>     mSessions;
    PairMap                                         mPairs;
    std::atomic<int64_t>                            mIdCounter;
    std::mutex                                      mSessionMutex, mPairMutex;
    std::thread                                     mCleaner;

public:
    SessionManager() : mIdCounter(0)
    {
        mCleaner = std::thread([this]() {
            std::chrono::seconds dura(60);
            std::this_thread::sleep_for(dura);
            clearExpiredSessions();
        });
    }

    virtual ~SessionManager() {}

    std::shared_ptr<Session>    createSession(const std::string& role, SEX sex, SEX seek);
    std::shared_ptr<Session>    findSession(int64_t id);
    bool                        seekPair(int64_t id);
    int64_t                     getSessionPair(int64_t id);
    void                        clearExpiredSessions();
    int64_t                     getSessionCount() const;
    void                        closeSession(int64_t id);
};
