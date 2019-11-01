
package com.market.hjapp.ui.tasks;

import java.util.HashMap;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.DialogInterface.OnCancelListener;
import android.os.AsyncTask;

import com.market.hjapp.MyLog;
import com.market.hjapp.R;

public abstract class BaseAsyncTask extends AsyncTask<String, Void, HashMap<String, Object>> {

    private static final String TAG = "BaseAsyncTask";
	protected ProgressDialog mProgress;
    protected Activity mActivity;
    
    protected TaskResultListener mListener;
    protected boolean mShowProgress;
    
    public BaseAsyncTask(Activity a, TaskResultListener l, boolean showProgress) {
        mActivity = a;
        mListener = l;
        mShowProgress = showProgress;
    }
    
    public BaseAsyncTask(Activity a, TaskResultListener l) {
        // by default, show progress
        this(a, l, true);
    }

    @Override
    protected void onPreExecute() {
        if (mShowProgress) {
            mProgress = new ProgressDialog(mActivity);
            mProgress.setMessage(mActivity.getString(R.string.processing));
            mProgress.setOnCancelListener(new OnCancelListener() {
    
                @Override
                public void onCancel(DialogInterface dialog) {
                    cancel(true);
                }
    
            });
            mProgress.show();
        }
    }

    @Override
    protected void onPostExecute(HashMap<String, Object> result) {
        try {
        	if (mProgress != null) mProgress.cancel();
        } catch (IllegalArgumentException e) {
        	MyLog.e(TAG, "Got Exception", e);
        }
        
        if (result == null || result.isEmpty()) {
            mListener.onTaskResult(false, null);
            return;
        }
       
        mListener.onTaskResult((Boolean)result.get("reqsuccess"), result);
    }

    public interface TaskResultListener {
        public void onTaskResult(boolean success, HashMap<String, Object> res);
    }
}
