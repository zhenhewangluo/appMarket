
package com.market.hjapp.ui.activity;

import android.os.Bundle;
import android.preference.PreferenceActivity;
import android.view.KeyEvent;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.R;

public class SettingActivity extends PreferenceActivity {
	
		public static final String TAG = "SettingActivity";

		
        @Override
        protected void onCreate(Bundle savedInstanceState) {
            // TODO Auto-generated method stub
            super.onCreate(savedInstanceState);
            addPreferencesFromResource(R.layout.menu_setting);
            
            saveUserLog(0);
            
            initValueOfDisplayImage = GeneralUtil.needDisplayImg(this);
            initValueOfRecommendAppNotificattion = GeneralUtil.needShowRecommendAppNotificattion(this);
            initValueOfVoiceNotificattion = GeneralUtil.needVoiceNotificattion(this);
        }
        
        boolean initValueOfDisplayImage = false;
        boolean initValueOfRecommendAppNotificattion = false;
        boolean initValueOfVoiceNotificattion = false;
        
        @Override
        public boolean onKeyDown(int keyCode, KeyEvent event) {
            if (keyCode == KeyEvent.KEYCODE_BACK) {
            	
            	if (initValueOfDisplayImage != GeneralUtil.needDisplayImg(this))
            		saveUserLog(1);
            	
            	if (initValueOfRecommendAppNotificattion != GeneralUtil.needShowRecommendAppNotificattion(this))
            		saveUserLog(2);
            	
            	if (initValueOfVoiceNotificattion != GeneralUtil.needVoiceNotificattion(this))
            		saveUserLog(3);
            }

            return super.onKeyDown(keyCode, event);
        }

        private void saveUserLog(int action)
        {
        	// save user log
    		GeneralUtil.saveUserLogType3(this, 26, action);
//    		if (action==0) {
//    			tracker.trackPageView("/"+TAG);
//    		}
//    		else {
//    			tracker.trackEvent(""+3, ""+26, "", action);
//    		}
        }



}