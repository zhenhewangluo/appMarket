package com.market.hjapp.tyxl;

import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.market.hjapp.R;
public class ViewCache {

	    private View baseView;
	    private TextView textView;
	    private TextView jifen;
	    private TextView jianjie;
	    private TextView yidui;
	    private TextView shengyu;
	    private ImageView imageView;
	    private Button button;

	    public ViewCache(View baseView) {
	        this.baseView = baseView;
	    }

	    public TextView getTextView() {
	        if (textView == null) {
	            textView = (TextView) baseView.findViewById(R.id.adtitle);
	        }
	        return textView;
	    }
	    
	    public TextView getJiFenView() {
	        if (jifen == null) {
	        	jifen = (TextView) baseView.findViewById(R.id.jifen);
	        }
	        return jifen;
	    }
	    
	    public TextView getJianJieView() {
	        if (jianjie == null) {
	        	jianjie = (TextView) baseView.findViewById(R.id.jianjie);
	        }
	        return jianjie;
	    }
	    
	    public TextView getOldNumView() {
	        if (yidui == null) {
	        	yidui = (TextView) baseView.findViewById(R.id.yidui);
	        }
	        return yidui;
	    }
	    
	    public TextView getNewNumView() {
	        if (shengyu == null) {
	        	shengyu = (TextView) baseView.findViewById(R.id.shengyu);
	        }
	        return shengyu;
	    }
	    
	    public ImageView getImageView() {
	        if (imageView == null) {
	            imageView = (ImageView) baseView.findViewById(R.id.logotypes);
	        }
	        return imageView;
	    }
	    public Button getButtonView() {
	        if (button == null) {
	        	button = (Button) baseView.findViewById(R.id.enter);
	        }
	        return button;
	    }
}


