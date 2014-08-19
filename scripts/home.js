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

function selectPlayerRole(src, role)
{
    $('#role-select-player').attr('src', src).attr('role', role);
    $('#selected-role').attr('value', role);
}

function selectMatchRole(src, role)
{
    $('#role-select-match').attr('src', src).attr('role', role);
    $('#selected-match').attr('value', role);
}

$(document).ready(function() {
    $('#role-select-player').tooltipster({
        trigger: 'click',
        content: $('#role-select-player-list').html(),
        theme: 'tooltipster-shadow',
        contentAsHTML: true,
        position: 'top',
        interactive: true,
        functionReady: function() {
          $('.role-select-player').click(function(){
            selectPlayerRole($(this).attr('src'), $(this).attr('role'));
          })
        }
    });
    $('#role-select-match').tooltipster({
        trigger: 'click',
        content: $('#role-select-match-list').html(),
        theme: 'tooltipster-shadow',
        contentAsHTML: true,
        position: 'top',
        interactive: true,
        functionReady: function() {
          $('.role-select-match').click(function(){
            selectMatchRole($(this).attr('src'), $(this).attr('role'));
          })
        }
    });
    $('#role-select-submit').click(function(){
      $('#role-select-form').submit();
    })
});
