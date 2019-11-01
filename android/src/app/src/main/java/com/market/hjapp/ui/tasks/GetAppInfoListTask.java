
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import com.market.hjapp.network.NetworkManager;

import android.app.Activity;

public class GetAppInfoListTask extends BaseAsyncTask {

	private boolean mIsRunning = false;

    public GetAppInfoListTask(Activity a, TaskResultListener l) {
        super(a, l, true);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        mIsRunning = true;
        
        try {
            return NetworkManager.getAppInfoList(
                    mActivity, 
                    params[0]);    // app list string
   
        } catch (ClientProtocolException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        } catch (IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        } catch (JSONException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }

        return null;
    }

    @Override
    protected void onPostExecute(HashMap<String, Object> result) {
    	super.onPostExecute(result);
        
        mIsRunning = false;
    }
    
    public boolean isRunning() {
        return mIsRunning;
    }

}
