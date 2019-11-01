package com.market.hjapp.tyxl;

import android.app.Activity;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.view.Window;

public class AdPlatform extends Activity {

	public static final String Rms_Registration = "";
	public static final String Rms_JiZhuPassword = "JiZhuPassword4";
	public static boolean isRunLogo0 = true;
	public static boolean isRunLogo1 = true;
	public static int SCREEN_WIDTH = 0;
	public static int SCREEN_HEIGHT = 0;
	public static String userId = "";
	public static String phone = "";
	public static String password = "";
	public static int mainState = 10;
	public static long startTime = 0;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		requestWindowFeature(Window.FEATURE_NO_TITLE);
		DisplayMetrics dm = new DisplayMetrics();
		getWindowManager().getDefaultDisplay().getMetrics(dm);
		SCREEN_WIDTH = dm.widthPixels;
		SCREEN_HEIGHT = dm.heightPixels;

	}
}
