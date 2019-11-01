
package com.market.hjapp.ui.tasks;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;

import android.app.Activity;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;

import com.market.hjapp.MyLog;

public class LaunchAppTask extends BaseAsyncTask {

    private static final String TAG = "LaunchAppTask";

    public LaunchAppTask(Activity a, TaskResultListener l) {
        super(a, l);
    }

    @Override
    protected HashMap<String, Object> doInBackground(String... params) {
        PackageManager pm = mActivity.getPackageManager();

        Intent mainIntent = new Intent(Intent.ACTION_MAIN, null);
        mainIntent.addCategory(Intent.CATEGORY_LAUNCHER);

        List<ResolveInfo> appList = pm.queryIntentActivities(mainIntent, 0);
        Collections.sort(appList, new ResolveInfo.DisplayNameComparator(pm));

        String appPackageName = params[0];
        String packageName = null;
        String name = null;
        for(int i=0; i<appList.size(); i++){
            MyLog.d(TAG, "number: " + i + "\n" +
                    "Name: " + appList.get(i).loadLabel(pm));
            packageName = appList.get(i).activityInfo.applicationInfo.packageName;
            if (packageName.equals(appPackageName)) {
                name = appList.get(i).activityInfo.name;
                
                HashMap<String, Object> res = new HashMap<String, Object>();
                res.put("reqsuccess", true);
                res.put("package_name", packageName);
                res.put("name", name);
                
                return res;
            }
        }
        
        MyLog.e(TAG, "Can't find installed package for: " + appPackageName);
        
        return null;
    }

}
