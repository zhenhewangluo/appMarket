
package com.market.hjapp.ui.view;

import java.io.File;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;

import android.content.Context;

import android.content.DialogInterface;
import android.preference.DialogPreference;
import android.util.AttributeSet;
import android.view.KeyEvent;

public class MyDialogPreference extends DialogPreference  {

    protected static final String TAG = "MyDialogPreference";
    
    private Context mContext;
    public MyDialogPreference(Context context, AttributeSet attrs) {
        super(context, attrs);
        
        mContext = context;
        saveUserLog(0);
    }
    
    @Override
    public void onClick (DialogInterface dialog, int which) 
    {
    	MyLog.d(TAG, "which:"+ which);
    	if (which == DialogInterface.BUTTON_POSITIVE) {
    		
    		saveUserLog(1);
    		
    		new Thread(new Runnable() {
    			
    			@Override
                public void run() {
		    		try{
						File directory = new File(GeneralUtil.LOCAL_DOWNLOAD_IMAGE_PATH);
		
						if (directory.exists()) {
			            	for (File f : directory.listFiles())
			            	{
			            		f.delete();
			            	}
			            }
			            
		    		}catch(Exception e)
		    		{
		    			MyLog.e(TAG, e.toString());
		    		}
    			}
    		}).start();
    	}
    	else
    	{
    		saveUserLog(2);
    	}
    }
    
    
    private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(mContext, 27, action);
    }

}
