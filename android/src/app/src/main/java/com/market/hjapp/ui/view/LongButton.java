package com.market.hjapp.ui.view;

import android.content.Context;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.market.hjapp.R;

public class LongButton extends RelativeLayout {

	protected static final String TAG = "LongButton";
	private TextView mText;
	private OnClickListener mListener;
	// private ImageView mLeftBg;
	// private ImageView mRightBg;
	private ImageView mCenterBg;

	public LongButton(Context context, AttributeSet attrs) {
		super(context, attrs);
	}

	private boolean mHightLight = false;
	public void setHighLightButton(boolean needHightLight) {
		mHightLight = needHightLight;
		// if (mHightLight)
		// {
		// mLeftBg.setImageResource(R.drawable.z61);
		// mCenterBg.setImageResource(R.drawable.z62);
		// mRightBg.setImageResource(R.drawable.z63);
		// }
		// else
		// {
		// mLeftBg.setImageResource(R.drawable.z36);
		// mCenterBg.setImageResource(R.drawable.z37);
		// mRightBg.setImageResource(R.drawable.z38);;
		// }
	}

	@Override
	protected void onFinishInflate() {
		super.onFinishInflate();
		mText = (TextView) findViewById(R.id.content);
		mCenterBg = (ImageView) findViewById(R.id.center_bg);

		setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {
				int action = event.getAction();

				// FIXME: current has a bug that even if user moved out of the
				// button, the click will still be triggered.
				// MyLog.d(TAG, "action >> " + action);
				// MyLog.d(TAG, "raw x: " + event.getRawX() + ", x: " +
				// event.getX() + ", x precision: " + event.getXPrecision());

				switch (action) {
					case MotionEvent.ACTION_DOWN :
//						mCenterBg
//						.setBackgroundResource(R.drawable.btn_long_pressed);
						// if (mHightLight) {
						// mLeftBg.setImageResource(R.drawable.z64);
						// mCenterBg.setImageResource(R.drawable.z65);
						// mRightBg.setImageResource(R.drawable.z66);
						// } else {
						// mLeftBg.setImageResource(R.drawable.z33);
						// mCenterBg.setImageResource(R.drawable.z34);
						// mRightBg.setImageResource(R.drawable.z35);
						// }
						//
						break;
					case MotionEvent.ACTION_UP :
//						mCenterBg
//						.setBackgroundResource(R.drawable.btn_long_normal);
						// if (mHightLight) {
						// mLeftBg.setImageResource(R.drawable.z61);
						// mCenterBg.setImageResource(R.drawable.z62);
						// mRightBg.setImageResource(R.drawable.z63);
						// } else {
						// mLeftBg.setImageResource(R.drawable.z36);
						// mCenterBg.setImageResource(R.drawable.z37);
						// mRightBg.setImageResource(R.drawable.z38);
						// }

						if (mListener != null)
							mListener.onClick(LongButton.this);
						break;
					default :
						return false;
				}
				return true;
			}

		});
	}

	public void setText(int res) {
		mText.setText(res);
	}

	public void setTextColor(int color) {
		mText.setTextColor(color);
	}

	public void setOnClickListener(OnClickListener listener) {
		mListener = listener;
	}
}
