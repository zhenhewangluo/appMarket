
package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.tasks.CommentTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class CommentActivity extends BaseActivity {
    private static final String TAG = "CommentActivity";
    
    private static final int REQUEST_LOGIN = 100;
    
    private TaskResultListener mCommentTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			MyLog.d(TAG, "onTaskResult");
			
			if (!success) {
				
				if (res == null)
				{
					Toast.makeText(getApplicationContext(), R.string.error_http_timeout , Toast.LENGTH_LONG).show();
				}
				else
				{
					String error = (String)res.get("errno");
	                if (error.equals("E012")) {
				    	//need login
	                	Intent i = new Intent(getApplicationContext(), LoginDialogActivity.class);
	                    
	                    i.putExtra("hint", getString(R.string.login_hint_comment));
	                    i.putExtra("page_no", 12);
	                    
	                    startActivityForResult(i, REQUEST_LOGIN);
				    }
	                else
	                {
	                	Toast.makeText(getApplicationContext(), R.string.comment_failed , Toast.LENGTH_LONG).show();
	                }
				}
				
			}
			else
			{	
				int appid = Integer.parseInt((String)res.get("appid"));
//				int ratingUp = (Integer)res.get("rating_up");
//				int ratingDown = (Integer)res.get("rating_down");
				
				//int score = (Integer)res.get("total_score");
				int commentCount = (Integer)res.get("total_comment_num"); 
				
				//int commentCount = (Integer)res.get("comment_Count");//mApp.getCommentCount()
				
				if (mDb.isOpen()) {
					DatabaseUtils.updateAppCommentCountCnt(mDb, appid, commentCount);//score, scoreCount);
	            } else {
	            	MyLog.d(TAG, "closed database");
	            	// db has been closed, possible this activity has been destroyed
	            	SQLiteDatabase db = new DatabaseHelper(getApplicationContext())
	            			.getWritableDatabase();
	            	//DatabaseUtils.updateAppRatingCnt(db, appid, score, scoreCount);
	            	DatabaseUtils.updateAppCommentCountCnt(db, appid, commentCount);
	            	db.close();
	            	return;
	            }
				
				Toast.makeText(CommentActivity.this, R.string.comment_successful, Toast.LENGTH_LONG).show();
				
				setResult(RESULT_OK);
				finish();
			}
		}
    	
    };

    
    private int mAppid;
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
    	
        super.onCreate(savedInstanceState);
        
//        getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
//                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        
        setContentView(R.layout.comment);
        
        mAppid = getIntent().getIntExtra("appid", -1);
        
        saveUserLog(0);
        
        initViews();       
        
    }
    
    private TextView mRateHint; 
    private EditText mCommentEdit;    
    private LongButton mCommentBtn;
    private LongButton mCancelBtn;
    private ImageView[] mRatingStar;
    
    
    private void initViews() {
    	mCommentEdit = (EditText)findViewById(R.id.comment_edit);
    	mRateHint = (TextView)findViewById(R.id.rating_ref_text);

        
    	mCommentBtn = (LongButton)findViewById(R.id.update_comment_btn);
    	mCommentBtn.setOnClickListener(mCommentBtnListener);
    	mCommentBtn.setBackgroundResource(R.drawable.btn_long_selector);
    	mCommentBtn.setText(R.string.comment);
    	
    	mCancelBtn = (LongButton)findViewById(R.id.cancel_btn);
    	mCancelBtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				setResult(RESULT_CANCELED);
				finish();
			}
		});
    	mCancelBtn.setBackgroundResource(R.drawable.btn_cancel_selector);
    	mCancelBtn.setText(R.string.cancel);
    }
    
    OnClickListener mCommentBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	saveUserLog(3);
        
        	if (!mCommentEdit.getText().toString().trim().equals(""))
        	{
        		saveUserLog(2);
        	}
        	else
        	{
        		Toast.makeText(CommentActivity.this, R.string.comment_no_comment, Toast.LENGTH_LONG).show();
        		return;
        	}
        	
        	try
    		{
        		MyLog.d(TAG, "intput comment content:"+mCommentEdit.getText().toString());
    			//TODO	
    			new CommentTask(CommentActivity.this, mCommentTaskResultListener)
        			.execute(mAppid+"","0",mCommentEdit.getText().toString());
    		} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };
    
    
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    	
    	if (requestCode == REQUEST_LOGIN) {
			if (resultCode == RESULT_OK) {
				
            }
			else
			{
				finish();
			}
		}

        super.onActivityResult(requestCode, resultCode, data);
    }
    
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
        	saveUserLog(4);
        }

        return super.onKeyDown(keyCode, event);
    }

	private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(getApplicationContext(), 11, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+11, "", action);
//		}
    }

}
