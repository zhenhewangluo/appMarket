
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;

import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;

public class BrowseManageListActivity extends BaseBottomTabActivity {
    private static final String TAG = "BrowseManageListActivity";
    private ListView mContentList;
    private int mCurTab = -1;
    
    private static final int REQUEST_BACKUP = 100;
    private static final int REQUEST_RECOVERY = 101;

    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            
        	saveUserLog(1);
        	Intent i=null;
            if(position==0){
            	saveUserLog(1);
            	
            	i=new Intent(getApplicationContext(), MyDownloadsActivity.class);
            	i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_downloads));
            	i.putExtra("goto_hasupdate_cate", false);
            	startActivity(i);
            }
            else if(position==1){
            	
            	saveUserLog(2);
        		if (GeneralUtil.getHasLoggedIn(BrowseManageListActivity.this)) {
            		i = new Intent(BrowseManageListActivity.this, BackupAppListActivity.class);
                	i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_backup));
            	    startActivity(i);
                	
                } else {
                	
            		i = new Intent(BrowseManageListActivity.this, LoginDialogActivity1.class);
                	i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_backup));
            		i.putExtra("hint", getString(R.string.login_hint_backup));
                	startActivityForResult(i, REQUEST_BACKUP);
//            		startActivity(i);
                }
            }
            else if(position==2){
            	
            	saveUserLog(3);
            	
            	if (GeneralUtil.getHasLoggedIn(BrowseManageListActivity.this)) {
            		i = new Intent(BrowseManageListActivity.this, RecoveryAppListActivity.class);
            		i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_recovery));
            	    startActivity(i);
                		
                } else {
                	
            		i = new Intent(BrowseManageListActivity.this, LoginDialogActivity1.class);
            		i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_recovery));
            		i.putExtra("hint", getString(R.string.login_hint_recovery));
                	startActivityForResult(i, REQUEST_RECOVERY);
//            		startActivity(i);
                }
               
            }else if(position==3){//分享欢聚宝 herokf add
            	saveUserLog(4);

				final Intent shareIntent = new Intent(
						Intent.ACTION_SEND);
				shareIntent.setType("text/plain");

				UserInfo user = GeneralUtil.getUserInfo(BrowseManageListActivity.this);
				String userName = user.getName();
				if (userName == null)
					userName = getString(R.string.share_via_email_sharer);

				shareIntent.putExtra(
						Intent.EXTRA_SUBJECT,"欢聚宝分享");
				shareIntent.putExtra(
						Intent.EXTRA_TEXT,
						getString(R.string.share_hj_content));

				startActivity(shareIntent.createChooser(shareIntent,
						getString(R.string.choose_share_client)));
            	
            }
        }
        
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        MyLog.d(TAG, "BrowseManageListActivity onCreate>>>>>>>>>>>>>>>>>>>>");
//        setContentView(R.layout.browse_manage_list_activity);
        
        initHeaderTabs();

        mContentList = (ListView)findViewById(R.id.contentList);
        mContentList.setSelector(R.drawable.c5);
        
        ArrayList<HashMap<String, Object>> listItem = new ArrayList<HashMap<String, Object>>();   

        HashMap<String, Object> map1 = new HashMap<String, Object>();   
        map1.put("ItemImage",R.drawable.icon_downloads);
        map1.put("ItemTitle", getString(R.string.title_downloads));
        listItem.add(map1);
        
        HashMap<String, Object> map2 = new HashMap<String, Object>();   
        map2.put("ItemImage",R.drawable.icon_backup);  
        map2.put("ItemTitle", getString(R.string.title_backup));  
        listItem.add(map2);
        
        HashMap<String, Object> map3 = new HashMap<String, Object>();
        map3.put("ItemImage",R.drawable.icon_recovery);
        map3.put("ItemTitle", getString(R.string.title_recovery));
        listItem.add(map3);
        
        HashMap<String, Object> map4 = new HashMap<String, Object>();
        map4.put("ItemImage",R.drawable.icon_share);
        map4.put("ItemTitle", getString(R.string.title_share));
        listItem.add(map4);
        
        SimpleAdapter listItemAdapter = new SimpleAdapter(this,listItem,
            R.layout.manage_item,
            new String[] {"ItemImage","ItemTitle"},
            new int[] {R.id.icon,R.id.manage_name}
        );   

        mContentList.setAdapter(listItemAdapter);
        mContentList.setOnItemClickListener(mListItemClickListener);
        
        setSelectedFooterTab(4);
    }

    private void initHeaderTabs() {
    	Button button = (Button)findViewById(R.id.btn_right);
		button.setVisibility(View.VISIBLE);
		button.setOnClickListener(this.backBtnOnClickListener);
    }

    protected void setSelectedHeaderTab(int i) {
    	if (mCurTab == i) return;
    	
        mCurTab = i;
       	saveUserLog(0);
        mContentList.setSelection(0);
    }
    
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    	
    	if (requestCode == REQUEST_BACKUP) {
			if (resultCode == RESULT_OK) {
				Intent i = new Intent(BrowseManageListActivity.this, BackupAppListActivity.class);
				i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_backup));
                startActivity(i);
                requestCode = -1;
            }
		}
    	if (requestCode == REQUEST_RECOVERY) {
			if (resultCode == RESULT_OK) {
				Intent i = new Intent(BrowseManageListActivity.this, RecoveryAppListActivity.class);
				i.putExtra(EXTRA_KEY_PARENTNAME, getString(R.string.title_recovery));
                startActivity(i);
                requestCode = -1;
            }
		}
        super.onActivityResult(requestCode, resultCode, data);
    }
    
    @Override
    protected int getLayout() {
        return R.layout.browse_manage_list_activity;
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            Intent i ;
            i = new Intent(BrowseManageListActivity.this, BrowseSuggestedAppListActivity.class);
            startActivity(i);
            finish();
            return true;
        }

        return super.onKeyDown(keyCode, event);
    }
    
    // 	save user log
    private void saveUserLog(int action)
    {
    	if (mCurTab == 0)
    	{  		
    		GeneralUtil.saveUserLogType3(getApplicationContext(), 42, action);
//    		if (action==0) {
//				tracker.trackPageView("/"+TAG);
//			}
//    		else {
//    			tracker.trackEvent(""+3, ""+42, "", action);
//			}
//    		MyLog.d(TAG, "saveUserLogType3,page=42,action="+action);
    	}
		
    }
}
