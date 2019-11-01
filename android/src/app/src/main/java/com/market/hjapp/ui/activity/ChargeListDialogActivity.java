
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.ChargeChannel;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.adapter.ChargeListAdapter;
import com.market.hjapp.ui.tasks.GetChargeListTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

public class ChargeListDialogActivity extends BaseActivity {
    private static final String TAG = "ChargeListDialogActivity";
    
    private ChargeListAdapter mListAdatper;
    
    private TaskResultListener mGetChargeListTaskResultListener = new TaskResultListener() {

		@SuppressWarnings("unchecked")
		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			if (!success) {
				
				Toast.makeText(getApplicationContext(), R.string.error_charge_list, Toast.LENGTH_LONG).show();
				
				setResult(RESULT_CANCELED);
				finish();
			}
			else
			{				
//				chargeListName = (ArrayList<String>)res.get("ChargeListNmae");
//				chargeListId = (ArrayList<String>)res.get("chargeListId");
				
				ListView list=(ListView)findViewById(R.id.charge_list);
//		        adapter = new ArrayAdapter<String>(ChargeListDialogActivity.this,
//							        android.R.layout.simple_list_item_1,
//							        chargeListName);
//		        list.setAdapter(adapter);
		        
				data = (ArrayList<ChargeChannel>)res.get("list");
				
		        mListAdatper = new ChargeListAdapter(ChargeListDialogActivity.this);
		        mListAdatper.setData(data);
		        list.setAdapter(mListAdatper);
		        
		        list.setOnItemClickListener(mListItemClickListener);
				
			}
		}
    	
    };
    
    ArrayList<ChargeChannel> data;
//    ArrayList<String> chargeListName = new ArrayList<String>();
//    ArrayList<String> chargeListId = new ArrayList<String>();
    
//    ArrayAdapter<String> adapter=null;
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        
        setContentView(R.layout.charge_list_dialog);
        
        try
        {
        	// start to get charge list
        	new GetChargeListTask(ChargeListDialogActivity.this, mGetChargeListTaskResultListener)
        	.execute();
        } catch (RejectedExecutionException e) {
        	MyLog.e(TAG, "Got exception when execute asynctask!", e);
        }
    }
    
    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
           
        	GeneralUtil.setDefaultChargeChannel( data.get(position).getName(), data.get(position).getId());
            
            setResult(RESULT_OK);
            finish();
        }
        
    };
    
}
