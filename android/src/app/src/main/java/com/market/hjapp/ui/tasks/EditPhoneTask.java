
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import com.market.hjapp.network.NetworkManager;

import android.app.Activity;

public class EditPhoneTask extends BaseAsyncTask {

    public EditPhoneTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... arg0) {
        try {
            return NetworkManager.setPhone(mActivity, 
                    arg0[0],    // phone
                    arg0[1]  // Validate
                   ); 
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
