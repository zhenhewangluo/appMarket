
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.network.NetworkManager;

public class UpdateDownloadDynamicTask extends BaseAsyncTask {

    private static final String TAG = "UpdateDownloadDynamicTask";

    public UpdateDownloadDynamicTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
    	HashMap<String, Object> datas = null;

        try {
            datas = NetworkManager.getEventTimes(mActivity, 
                    params[0]
            );
                                   

            return datas;
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
