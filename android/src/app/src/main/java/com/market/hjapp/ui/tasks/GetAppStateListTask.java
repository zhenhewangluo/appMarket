
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

public class GetAppStateListTask extends BaseAsyncTask {

    public GetAppStateListTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        try {
            return NetworkManager.getAppStateList(
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

}
