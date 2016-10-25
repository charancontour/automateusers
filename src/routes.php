<?php
Route::get('automate-users/{id}','Automateusers\userAutomationController@branchusers');
Route::get('automate-users-insert','Automateusers\userAutomationController@prefixLogin');
