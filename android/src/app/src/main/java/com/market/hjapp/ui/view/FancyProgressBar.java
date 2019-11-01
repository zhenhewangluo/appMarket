
package com.market.hjapp.ui.view;

import android.content.Context;
import android.util.AttributeSet;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.TextView;

import com.market.hjapp.MyLog;
import com.market.hjapp.R;

public class FancyProgressBar extends FrameLayout {

    private static final String TAG = "FancyProgressBar";
    
    private View mNormalProgress;
    private View mPausedProgress;

    private View mProgressView;
    private View mBlankView;

    private TextView mProgressText;
    private View mProgress;

    public FancyProgressBar(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    @Override
    protected void onFinishInflate() {
//        mNormalProgress = findViewById(R.id.normal_progress);
//        mPausedProgress = findViewById(R.id.paused_progress);
//        
//        mProgressView = findViewById(R.id.progress);
//        mBlankView = findViewById(R.id.blank);
        
    	mProgress = findViewById(R.id.progress);
        mProgressText = (TextView)findViewById(R.id.percent);
        super.onFinishInflate();
    }
    
    private static final float BASE_WIDTH = 162;
    private static final float BASE_HEIGHT = 130;

    public void setProgress(boolean isPaused, int progress) {//Loading进度条 读取服务器资源
        MyLog.d(TAG, "set progress: " + progress);
        
        ViewGroup.LayoutParams p = mProgress.getLayoutParams();
        
        final float scale = getContext().getResources().getDisplayMetrics().density;
        int width = (int) (BASE_WIDTH * scale + 0.5f);
        
        p.width = width * progress /100;
        
        mProgress.setLayoutParams(p);
        mProgressText.setLayoutParams(p);
        
        mProgressText.setText(progress + " %");
    }
    public void setProgress(int progress) {//Loading进度条 读取服务器资源
    	MyLog.d(TAG, "set progress: " + progress);
    	ViewGroup.LayoutParams p = mProgress.getLayoutParams();
    	
    	final float scale = getContext().getResources().getDisplayMetrics().density;
    	int height = (int) (BASE_HEIGHT * scale + 0.5f);
    	
    	p.height -= height * progress /100;
    	mProgress.setLayoutParams(p);
//    	mProgressText.setText(progress + " %");
    }
}
