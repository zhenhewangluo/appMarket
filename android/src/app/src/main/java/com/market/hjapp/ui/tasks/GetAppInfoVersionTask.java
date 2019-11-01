package com.market.hjapp.ui.tasks;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;
import android.database.sqlite.SQLiteDatabase;
import android.os.AsyncTask;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.MyLog;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

public class GetAppInfoVersionTask extends BaseAsyncTask {

	private boolean mIsRunning = false;

	public GetAppInfoVersionTask(Activity a, TaskResultListener l) {
		super(a, l, false);
		// TODO Auto-generated constructor stub
	}

	@Override
	protected HashMap<String, Object> doInBackground(String... params) {
		mIsRunning = true;
		 try {
			return NetworkManager.getInfoVersion(
			         mActivity, 
			         params[0], //appid
			         params[1]); //infoversion
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
