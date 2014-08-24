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

std::shared_ptr<Session> SessionManager::createSession(const std::string& role, SEX sex, SEX seek)
{
    auto ptr = std::make_shared<Session>(++mIdCounter, role, sex, seek);
    
    {
        std::unique_lock<std::mutex> lock(mSessionMutex);
        mSessions.insert(std::make_pair(ptr->getId(), ptr));
    }

    return ptr;
}

std::shared_ptr<Session> SessionManager::findSession(int64_t id)
{
    // should share
    std::unique_lock<std::mutex> lock(mSessionMutex);
    auto iter = mSessions.find(id);
    if(iter == mSessions.end())
    {
        return std::shared_ptr<Session>();
    }
    else
    {
        return iter->second;
    }
}

bool SessionManager::seekPair(int64_t id)
{
    if(getSessionPair(id) != -1)
    {
        return true;
    }

    // should share
    std::unique_lock<std::mutex> lock(mSessionMutex);

    auto iter = mSessions.find(id);
    if(iter == mSessions.end())
    {
        return false;
    }

    auto ptr = iter->second;
    ptr->ping();
    int64_t paired = -1;

    for(auto i : mSessions)
    {
        if(i.second->getSelfSex() == ptr->getSeekSex() &&
            i.second->getSeekSex() == ptr->getSelfSex() &&
            i.second->getId() != ptr->getId() &&
            !i.second->expired() &&
            getSessionPair(i.second->getId()) == -1 &&
            !i.second->isDisabled())
        {
            paired = i.second->getId();
            lock.unlock();
            {
                std::unique_lock<std::mutex> lock(mPairMutex);
                mPairs.insert(Pair(ptr->getId(), paired));
            }
            return true;
        }
    }

    return false;
}

int64_t SessionManager::getSessionPair(int64_t id)
{
    // should share
    std::unique_lock<std::mutex> lock(mPairMutex);

    auto iterleft = mPairs.left.find(id);
    if(iterleft != mPairs.left.end())
    {
        return iterleft->second;
    }
    auto iterright = mPairs.right.find(id);
    if(iterright != mPairs.right.end())
    {
        return iterright->second;
    }
    return -1;
}

void SessionManager::clearExpiredSessions()
{
    std::unique_lock<std::mutex> lock(mSessionMutex);

    for(auto i = mSessions.begin(); i != mSessions.end(); ++i)
    {
        if(i->second->expired())
        {
            {
                std::unique_lock<std::mutex> lock(mPairMutex);
                mPairs.left.erase(i->second->getId());
                mPairs.right.erase(i->second->getId());
            }
            mSessions.erase(i++);
        }
    }
}

int64_t SessionManager::getSessionCount() const
{
    return mSessions.size();
}

void SessionManager::closeSession(int64_t id)
{
    {
        std::unique_lock<std::mutex> lock(mSessionMutex);
        mSessions.erase(id);
    }
    {
        std::unique_lock<std::mutex> lock(mPairMutex);
        auto li = mPairs.left.find(id);
        {
            if(li != mPairs.left.end())
            {
                auto pair = findSession(li->second);
                if(pair)
                {
                    pair->disable();
                }
                mPairs.left.erase(li);
            }
        }
        auto ri = mPairs.right.find(id);
        {
            if(ri != mPairs.right.end())
            {
                auto pair = findSession(ri->second);
                if(pair)
                {
                    pair->disable();
                }
                mPairs.right.erase(ri);
            }
        }
    }
}
