
package com.market.hjapp.service.download;

import java.io.File;

import com.market.hjapp.R;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.widget.Toast;

public class FileManipulation {
    private static final String TAG = "FileManipulation";
	private static String UNINSTALL_PACKAGE_PREFIX = "package:";
	
	//删除文件
	static boolean delFile(String path){
		File file = new File(path);
		return file.delete();
	}
	
	//安装文件
	static void install(String path,Context context) throws Exception{
		if(!path.endsWith(".apk")){
			throw new Exception("Invalidate File ...");
		}

		File file = new File(path);
		if(!file.exists()){
//			throw new RuntimeException("file " + path + " doesn't exist!");
			Toast.makeText(context, context.getString(R.string.error_installing_app_not_found), Toast.LENGTH_LONG).show();
			return;
		}
		Intent intent = new Intent();
		intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		intent.setAction(Intent.ACTION_VIEW);
		
		/* 设置intent的file与MimeType */
		String type = "application/vnd.android.package-archive";
		
		intent.setDataAndType(Uri.fromFile(file), type);
		context.startActivity(intent);
	
	}
	
	public static void uninstall(Context ctx, String packageName) {//卸载
		Uri packageURI = Uri.parse(UNINSTALL_PACKAGE_PREFIX + packageName);
    	
		Intent uninstallIntent = new Intent(Intent.ACTION_DELETE, packageURI);
    	ctx.startActivity(uninstallIntent);
	}
}
