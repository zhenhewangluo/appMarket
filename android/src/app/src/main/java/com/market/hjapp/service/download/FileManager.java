
package com.market.hjapp.service.download;

import android.content.Context;


public class FileManager {
	public static boolean delFile(String path){//删除
		return FileManipulation.delFile(path);
	}
	
	public static void install(String path,Context context) throws Exception{//安装
		FileManipulation.install(path, context);
	}
	
	public static void uninstall(String packageName,Context context){//卸载
		FileManipulation.uninstall(context, packageName);
	}
}
