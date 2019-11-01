
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.MyLog;
import com.market.hjapp.network.NetworkManager;

public class ProcessInstallTask extends BaseAsyncTask {

    private static final String TAG = "ProcessInstallTask";

    public ProcessInstallTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        HashMap<String, Object> datas = null;

        try {
            datas = NetworkManager.downloadApp(mActivity, 
                    params[0],      // app id 
                    params[1],      // pay id
                    params[2]);     // parent cate name

            if (params.length > 3)
            {
	            datas.put("dbid", params[3]);  // app dbid
	            datas.put("appid", params[0]); // app id
	            datas.put("name", params[4]);  // app name
	            datas.put("download_count", params[5]); // app download count
            }
            
            return datas;
        } catch (ClientProtocolException e) {
            MyLog.e(TAG, "", e);
        } catch (IOException e) {
            MyLog.e(TAG, ""+e.getMessage(), e);
        } catch (JSONException e) {
            MyLog.e(TAG, "", e);
        }

        return null;
    }

}
