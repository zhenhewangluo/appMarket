

package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.network.NetworkManager;

public class AnonymousLoginTask extends BaseAsyncTask {

    private static final String TAG = "InitializationTask";

    public AnonymousLoginTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... arg0) {
        try {
            // anonymous login
            HashMap<String, Object> anonymousLoginRes = NetworkManager.anonymousLogin(mActivity);
            if (anonymousLoginRes == null
                    || anonymousLoginRes.isEmpty()
                    || !(Boolean)anonymousLoginRes.get("reqsuccess")) {
                return null;
            }
            
            String mid = (String)anonymousLoginRes.get("mid");
            String sid = (String)anonymousLoginRes.get("sid");

            // save status and mid/sid
            GeneralUtil.saveLoginInfo(mActivity, mid, sid);
            
            int user_guide = (Integer)anonymousLoginRes.get("user_guide");
            GeneralUtil.saveUserGuideFunc(mActivity, user_guide);
            
            HashMap<String, Object> res = new HashMap<String, Object>();
            res.put("reqsuccess", true);
            
            return res;
        } catch (ClientProtocolException e) {
            MyLog.e(TAG, "", e);
        } catch (IOException e) {
            MyLog.e(TAG, "", e);
        } catch (JSONException e) {
            MyLog.e(TAG, "", e);
        }
        return null;
    }

}
