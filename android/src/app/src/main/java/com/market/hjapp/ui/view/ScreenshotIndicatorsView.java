
package com.market.hjapp.ui.view;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.market.hjapp.R;

public class ScreenshotIndicatorsView extends LinearLayout {

	private int mIndicatorCount;

	public ScreenshotIndicatorsView(Context context, AttributeSet attrs) {
		super(context, attrs);
	}

	public void setIndicatorCount(int c) {
		mIndicatorCount = c;

		Context ctx = getContext();

		for (int i = 0; i < c; i++) {
			ImageView v = new ImageView(ctx);
			v.setImageResource(R.drawable.m3);

			addView(v);
		}
	}

	public void setHightlightIndicator(int i) {
		for (int j = 0; j < mIndicatorCount; j++) {
			ImageView v = (ImageView)getChildAt(j);

			v.setImageResource((j == i) ? R.drawable.m2 : R.drawable.m3);
		}
	}
}
