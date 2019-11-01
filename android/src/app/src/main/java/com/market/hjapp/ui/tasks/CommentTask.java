
package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;

import com.market.hjapp.MyLog;
import com.market.hjapp.network.NetworkManager;

public class CommentTask extends BaseAsyncTask {

    private static final String TAG = "CommentTask";

    public CommentTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        HashMap<String, Object> datas = null;

        try {
            datas = NetworkManager.comment(mActivity,     
            		params[0],      // app id
            		params[1],      // rating
            		params[2]       // comments
            );
                                   

            return datas;
        } catch (ClientProtocolException e) {
            MyLog.e(TAG, "", e);
        } catch (IOException e) {
            MyLog.e(TAG, "", e);
        } catch (JSONException e) {
            MyLog.e(TAG, "", e);
        }

        return null;
    }

}
