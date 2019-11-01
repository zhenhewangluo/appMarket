package com.market.hjapp;

import android.util.Log;

public class MyLog {
	private static final boolean DEBUG = true;
	public static void d(String tag, String msg) {
		if (DEBUG) Log.d(tag, msg);
	}
	public static void e(String tag, String msg) {
		if (DEBUG) Log.e(tag, msg);
	}
	public static void ee(String Error) {
		if (DEBUG) Log.e(Error, "Error!!!!!!!!!!!!!!!!!!!!");
	}
	
	public static void e(String tag, String msg, Throwable e) {
		if (DEBUG) Log.e(tag, msg, e);
	}
	
	public static void i(String tag, String msg) {
		if (DEBUG) Log.i(tag, msg);
	}
	public static void v(String tag, String msg) {
		if (DEBUG) Log.v(tag, msg);
	}
}
