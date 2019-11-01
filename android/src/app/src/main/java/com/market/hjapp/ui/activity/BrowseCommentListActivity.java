
package com.market.hjapp.ui.activity;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.Comment;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.ui.adapter.CommentListAdapter;
import com.market.hjapp.ui.view.LongButton;

public class BrowseCommentListActivity extends BaseActivity {
    private static final String TAG = "commentListActivity";
    
    private static final int REQUEST_COMMENT = 100;
    
    ListView mContentList;
    CommentListAdapter mListAdatper;
    
    ArrayList<Comment> data;
    
    View mLoadingBackground;
    ProgressBar mProgressBar;
    TextView mBgText;
    TextView mTitleText;
    
    int mAppid;
    
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.comment_list);
        

        mAppid = getIntent().getIntExtra("appid", -1);
        
        mContentList = (ListView)findViewById(R.id.contentList);
        
        LongButton rate = (LongButton)findViewById(R.id.comment_rate_btn);
		rate.setText(R.string.comment_rating);
		rate.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View v) {
				
				Intent i = new Intent(getApplicationContext(), CommentActivity.class);
				i.putExtra("appid", mAppid);
				startActivityForResult(i, REQUEST_COMMENT);
			}
        });
		
		mLoadingBackground = findViewById(R.id.loading_bg);
		mProgressBar = (ProgressBar)findViewById(R.id.progress);
		mBgText = (TextView)findViewById(R.id.background_text);
		mTitleText = (TextView)findViewById(R.id.title_text);
		
		mTitleText.setText(getIntent().getStringExtra("appName"));
		
        data = new ArrayList<Comment>();
        
        mListAdatper = new CommentListAdapter(BrowseCommentListActivity.this);
        mContentList.setAdapter(mListAdatper);
        
        mListAdatper.setData(data);
        
        setContentListVisibility(false);
        
        try
        {
        	new LoadCommentListTask().execute(mAppid+"");
        } catch (RejectedExecutionException e) {
        	MyLog.e(TAG, "Got exception when execute asynctask!", e);
        }
    }
    
    private void setContentListVisibility(boolean showList) {
        mContentList.setVisibility(showList ? View.VISIBLE : View.GONE);
        mLoadingBackground.setVisibility(showList ? View.GONE : View.VISIBLE);
    }
    
    private class LoadCommentListTask extends AsyncTask<String, Void, HashMap<String, Object>> {
    	

        @Override
        protected HashMap<String, Object> doInBackground(String... params) {
            
            try {
                return  NetworkManager.getComments(BrowseCommentListActivity.this, params[0]);
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

        @SuppressWarnings("unchecked")
		@Override
        protected void onPostExecute(HashMap<String, Object> res) {
            if (res == null || res.isEmpty()) {
                MyLog.e(TAG, "Can't get comment list!");
                
                Toast.makeText(getApplicationContext(), R.string.error_http_timeout, Toast.LENGTH_LONG).show();
        		                
                finish();
                return;
            }
            
            boolean success = (Boolean)res.get("reqsuccess");
            if (!success) {
				
				String error = (String)res.get("errno");
				int errMsg;
                if (error.equals("E301")) {
                	
                	mProgressBar.setVisibility(View.GONE);
                	mBgText.setText(R.string.get_comments_no_comment);
                }
                else
                {
                	errMsg = R.string.get_comments_failed;
                	Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_LONG).show();
                	
                	finish();
                }
				
			}
			else
			{				
				ArrayList<Comment> commentList = (ArrayList<Comment>)res.get("list");
	            MyLog.d(TAG, "comment list size: " + commentList.size());
	            if (commentList == null || commentList.size() == 0) {
	                MyLog.e(TAG, "Can't get comment list!");
	                return;
	            }
	            
	            mListAdatper.setData(commentList);
	                
	            setContentListVisibility(true);
				
			}
            
        }
    }
    
    
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == REQUEST_COMMENT) {
        	finish();
        }

        super.onActivityResult(requestCode, resultCode, data);
    }

}