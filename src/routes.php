<?php 
Route::get('automate-users/{id}','automation\users\userAutomationController@branchusers');
Route::get('automate-users-insert','automation\users\userAutomationController@prefixLogin');
