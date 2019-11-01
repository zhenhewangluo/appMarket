
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.network.NetworkManager;

public class LoginTask extends BaseAsyncTask {

    public LoginTask(Activity activity, TaskResultListener listener) {
        super(activity, listener);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        try {
            return NetworkManager.login(mActivity, 
                    params[0], // username
                    params[1]); // password
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
