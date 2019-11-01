
package com.market.hjapp.ui.activity;

import android.os.Bundle;

import com.market.hjapp.*;

public class RSATestActivity extends BaseActivity {
    private static final String TAG = "RSATestActivity";

    @SuppressWarnings("unchecked")
    @Override 
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        try
        {
        	SecurityUtil.generatorRSAPairKey();
        }
        catch(Exception e)
        {
        	
        }
        
    }
    
    
}
