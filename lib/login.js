/**
 * 
 */
function login(options){
	options = options || {};
	var deferred = $.Deferred();
	var loginLogout = $(options.buttonSelector);
	var user = $(options.userSelector);
	var password = $(options.passwordSelector);
	$.couch.login({name:user.val(),password:password.val(),
		success: function(data,text,xhr){
			loginLogout.val("Logout").closest( options.containerSelector ).effect( options.containerEffect );
			user.prop("disabled", true);
			password.prop("disabled",true);
			deferred.resolve();
		},
		error: function(data,text,xhr){
			//show something nice
			$.when(loginLogout.click()).done(function(){
				deferred.resolve();	
			});
		}
	});
	return deferred.promise();
}
function logout(options){
	options = options || {};
	var deferred = $.Deferred();
	var loginLogout = $(options.buttonSelector);
	var user = $(options.userSelector);
	var password = $(options.passwordSelector);
	$.couch.session(
		{success:function(data){
			$.couch.logout(data);
			loginLogout.val("Login").closest( options.containerSelector ).effect( options.containerEffect );
			user.prop("disabled", false).val("");
			password.prop("disabled",false).val("");
			deferred.resolve();
		}}
	);
	return deferred.promise();
}
	

