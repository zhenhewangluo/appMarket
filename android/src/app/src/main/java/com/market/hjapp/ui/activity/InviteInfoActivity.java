
package com.market.hjapp.ui.activity;

import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.TextView;

import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.view.LongButton;

public class InviteInfoActivity extends BaseActivity {
	
	public static final String TAG = "InviteInfoActivity";
	
    private TextView mInviteInfo1;
    private TextView mInviteInfo2;
    
    private OnClickListener mInviteButtonListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	try
        	{
        		Intent i = new Intent(InviteInfoActivity.this, ChooseShareOptionDialogActivity.class);
                startActivity(i);
        	} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };
    

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
//        setContentView(R.layout.invite_info_activity);
//
//        mInviteInfo1 = (TextView)findViewById(R.id.invite_info1);
//        mInviteInfo2 = (TextView)findViewById(R.id.invite_info2);       
//        
//        mInviteInfo1.setText(R.string.invite_info1);
//        mInviteInfo2.setText(R.string.invite_info2);
//        
//        LongButton inviteBtn = (LongButton)findViewById(R.id.invite_btn);
//        inviteBtn.setText(R.string.invite_btn);
//        inviteBtn.setOnClickListener(mInviteButtonListener);

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        
         super.onActivityResult(requestCode, resultCode, data);
    }

}
