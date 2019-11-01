
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import com.market.hjapp.network.NetworkManager;

import android.app.Activity;

public class EditUserinfoTask extends BaseAsyncTask {

	public static final String USER_BOTH = "0";
	public static final String USER_ONLY_PHONE = "1";
	public static final String USER_ONLY_NICKNAME = "2";
    public EditUserinfoTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... arg0) {
        try {
            return NetworkManager.setUserInfo(mActivity, 
                    arg0[0],    // phone
                    arg0[1],  // name
                    arg0[2]); //type
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
