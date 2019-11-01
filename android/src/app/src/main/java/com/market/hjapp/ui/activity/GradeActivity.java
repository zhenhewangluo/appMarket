
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

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.tasks.CommentGradeTask;
import com.market.hjapp.ui.tasks.CommentTask;
import com.market.hjapp.ui.tasks.GetMyRatingTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class GradeActivity extends BaseActivity {
    private static final String TAG = "GradeActivity";
    private static final int REQUEST_COMMENT_GRADE = 105;
    
    
    private TaskResultListener mGetMyRatingTaskResultListener = new TaskResultListener() {

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
					
				}
				
			}
			else
			{	
				int score = (Integer)res.get("score");
				
				if (score != 0)
					setStar(score - 1);
			}
		}
    	
    };
    
    
    private TaskResultListener mGradeTaskResultListener = new TaskResultListener() {

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
//				    	//need login
//	                	Intent i = new Intent(getApplicationContext(), LoginDialogActivity.class);
//	                    
//	                    i.putExtra("hint", getString(R.string.login_hint_comment));
//	                    i.putExtra("page_no", 12);
//	                    
//	                    startActivityForResult(i, REQUEST_COMMENT_GRADE);
				    }
	                else
	                {
	                	Toast.makeText(getApplicationContext(), R.string.comment_grade_failed , Toast.LENGTH_LONG).show();
	                }
				}
				
			}
			else
			{	
				int appid = Integer.parseInt((String)res.get("appid"));
				
				int score = (Integer)res.get("total_score");
				int scoreCount = (Integer)res.get("total_num"); 
				
				if (mDb.isOpen()) {
					DatabaseUtils.updateAppScoreCnt(mDb, appid, score, scoreCount);
	            } else {
	            	MyLog.d(TAG, "closed database");
	            	// db has been closed, possible this activity has been destroyed
	            	SQLiteDatabase db = new DatabaseHelper(getApplicationContext())
	            			.getWritableDatabase();
	            	DatabaseUtils.updateAppScoreCnt(db, appid, score, scoreCount+1);
	            	db.close();
	            	return;
	            }
				
				Toast.makeText(GradeActivity.this, R.string.comment_grade_successful, Toast.LENGTH_LONG).show();
				
				setResult(RESULT_OK);

				finish();
			}
		}
    	
    };

    
    private int mAppid;
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
    	
        super.onCreate(savedInstanceState);
        
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.grade);
        mAppid = getIntent().getIntExtra("appid", -1);
        saveUserLog(0);
        initViews();    
        
        try
		{
			//TODO	
			new GetMyRatingTask(GradeActivity.this, mGetMyRatingTaskResultListener)
					.execute(mAppid+"");
    			//.execute(mAppid, (mRate + 0)+"", mCommentEdit.getText().toString());
		} catch (RejectedExecutionException e) {
        	MyLog.e(TAG, "Got exception when execute asynctask!", e);
        }
    }
    
    private TextView mRateHint; 
    private LongButton mCommentBtn;
    private ImageView[] mRatingStar;
    
    
    private void initViews() {
    	mRateHint = (TextView)findViewById(R.id.rating_ref_text);
    	
    	mRatingStar = new ImageView[5];
    	mRatingStar[0] = (ImageView)findViewById(R.id.star1);
    	mRatingStar[1] = (ImageView)findViewById(R.id.star2);
    	mRatingStar[2] = (ImageView)findViewById(R.id.star3);
    	mRatingStar[3] = (ImageView)findViewById(R.id.star4);
    	mRatingStar[4] = (ImageView)findViewById(R.id.star5);
    	
    	mRatingStar[0].setOnClickListener(new OnClickListener() {
	        @Override
	        public void onClick(View arg0) {
	        	setStar(0);
	        }
    	});
    	
    	mRatingStar[1].setOnClickListener(new OnClickListener() {
	        @Override
	        public void onClick(View arg0) {
	        	setStar(1);
	        }
    	});
    	
    	mRatingStar[2].setOnClickListener(new OnClickListener() {
	        @Override
	        public void onClick(View arg0) {
	        	setStar(2);
	        }
    	});
    	
    	mRatingStar[3].setOnClickListener(new OnClickListener() {
	        @Override
	        public void onClick(View arg0) {
	        	setStar(3);
	        }
    	});
    	
    	mRatingStar[4].setOnClickListener(new OnClickListener() {
	        @Override
	        public void onClick(View arg0) {
	        	setStar(4);
	        }
    	});
        
    	mCommentBtn = (LongButton)findViewById(R.id.update_comment_btn);
    	mCommentBtn.setOnClickListener(mCommentBtnListener);
    	mCommentBtn.setBackgroundResource(R.drawable.btn_long_selector);
    	mCommentBtn.setText(R.string.comment);
    	
    	setStar(-1);
    
    	userClickedStar = false;
    }
    
    boolean userClickedStar = false; 
    
    private int mRate = 2;
    private void setStar(int selectRate)
    {
    	userClickedStar = true;
    	
	    for (int i =0; i< 5; i++)
	    {
	    	if (i <= selectRate)
	    		mRatingStar[i].setImageResource(R.drawable.star_selected);
	    	else
	    		mRatingStar[i].setImageResource(R.drawable.star_normal);
	    }
	    
	    setRateText(selectRate);
	    
	    mRate = selectRate;
	    
    }
    
    private void setRateText(int selectRate)
    {
    	switch(selectRate)
    	{
    		case 0:
    			mRateHint.setText(R.string.rate_1star);
    			break;
    			
    		case 1:
    			mRateHint.setText(R.string.rate_2star);
    			break;
    			
    		case 2:
    			mRateHint.setText(R.string.rate_3star);
    			break;
    		
    		case 3:
    			mRateHint.setText(R.string.rate_4star);
    			break;
    		
    		case 4:
    			mRateHint.setText(R.string.rate_5star);
    			break;
    			
    		default:
    			mRateHint.setText(R.string.rate_no_star);
    			break;
    		
    	}
    }
    
    OnClickListener mCommentBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	
        	if (mRate == -1)
        	{
				Toast.makeText(GradeActivity.this, R.string.rate_need_select, Toast.LENGTH_LONG).show();
        		return;
        	}
        	
        	if (userClickedStar)
        	{
        		saveUserLog(1);
        	}
        	
        	try
    		{
    			new CommentGradeTask(GradeActivity.this, mGradeTaskResultListener)
    					.execute(mAppid+"", (mRate+1) + "");
        			//.execute(mAppid, (mRate + 0)+"", mCommentEdit.getText().toString());
    		} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };
    
    
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    	
    	if (requestCode == REQUEST_COMMENT_GRADE) {
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
        	saveUserLog(2);
        }

        return super.onKeyDown(keyCode, event);
    }

	private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(getApplicationContext(), 40, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+40, "", action);
//		}
    }

}
