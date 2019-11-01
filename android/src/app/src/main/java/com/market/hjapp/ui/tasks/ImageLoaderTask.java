
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.MyLog;
import com.market.hjapp.network.NetworkManager;

public class ImageLoaderTask extends BaseAsyncTask {

    private static final String TAG = "ImageLoaderTask";

    public ImageLoaderTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        HashMap<String, Object> datas = null;

        try {
            datas = NetworkManager.imageLoader(mActivity,     
            		params[0]      // image url
            );
                             
            if (datas == null)
            {
            	MyLog.d(TAG, "LOADING FAILED == " + params[0]);
            	datas = new HashMap<String, Object>();
            	datas.put("reqsuccess", false);
            	datas.put("context", mActivity);
            	datas.put("imageUrl", params[0] );
            }
            else
            	MyLog.d(TAG, "LOADING SUCESS == " + params[0]);

            return datas;
        } catch (ClientProtocolException e) {
            MyLog.e(TAG, "", e);
        } catch (IOException e) {
            MyLog.e(TAG, "", e);
        } catch (JSONException e) {
            MyLog.e(TAG, "", e);
        }

        datas = new HashMap<String, Object>();
        datas.put("reqsuccess", false);
    	datas.put("context", mActivity);
    	datas.put("imageUrl", params[0] );
    	
        return datas;
    }

}
