
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

import android.app.Activity;

public class GetOneCateDataTask extends BaseAsyncTask {

	private boolean mIsRunning = false;

    public GetOneCateDataTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        mIsRunning = true;
        
        try {
            return NetworkManager.getOneCateData(
                    mActivity, 
                    params[0]);    // cateid
   
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
