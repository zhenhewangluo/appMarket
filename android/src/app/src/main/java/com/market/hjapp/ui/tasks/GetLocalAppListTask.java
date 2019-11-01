
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.network.NetworkManager;

public class GetLocalAppListTask extends BaseAsyncTask {

    public GetLocalAppListTask(Activity a, TaskResultListener l) {
        super(a, l, false);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        try {
            return NetworkManager.getLocalApplist(
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
