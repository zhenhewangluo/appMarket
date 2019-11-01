
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.network.NetworkManager;

public class RegisterTask extends BaseAsyncTask {

    public RegisterTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... arg0) {
        try {
            return NetworkManager.register(
                    mActivity, 
                    arg0[0], // username
                    arg0[1],// password
                    arg0[2]
                    		); // nickname
            
           
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
