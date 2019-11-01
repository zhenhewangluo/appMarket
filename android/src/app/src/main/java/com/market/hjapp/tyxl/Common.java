package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Vector;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;

import com.market.hjapp.tyxl.object.Friend;

public class Common {
	public static Boolean onlineOrnot = true;

	public static void Finish(final Activity Act) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(Act);
		alertDialog.setTitle("退出程序");
		alertDialog.setMessage("是否退出程序");
		alertDialog.setPositiveButton("确定",
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						Act.finish();
						System.exit(0);
					}
				});
		alertDialog.setNegativeButton("取消",
				new DialogInterface.OnClickListener() {

					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话�?
		alertDialog.show(); // 显示对话�?
	}

	public static void WebFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle("提示");
		alertDialog.setMessage("联网失败！");
		alertDialog.setPositiveButton("确定",
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	// 是否为手机号码
	public static boolean IsUserNumber(String mobiles) {
		boolean re = false;
		if (mobiles.length() == 11) {
			if (mobiles.startsWith("13")) {
				re = true;
			} else if (mobiles.startsWith("15")) {
				re = true;
			} else if (mobiles.startsWith("18")) {
				re = true;
			}
		}
		return re;
	}

	// 是否在Vector有值
	public static boolean IsContainFriends(ArrayList<Friend> vector, String un) {
		for (int i = 0; i < vector.size(); i++) {
			if (un.equals(vector.get(i).friedsNumber)) {
				return true;
			}
		}
		return false;
	}

	// 是否在LIST有值
	public static boolean IsContain(List<Map<String, Object>> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).get("number"))) {
				return true;
			}
		}
		return false;
	}

	/*
	 * 切换Activity
	 */
	public static void ChangeActivity(Activity activity, Context context,
			Class<?> act1) {
		Intent intent = new Intent();
		intent.setClass(context, act1);
		activity.startActivity(intent);
		activity.finish();
	}

	/**
	 * 切割字符�?按特殊符�?
	 * 
	 * @param str
	 * @return
	 */
	public static String[] mySplict(String str, char chr) {
		/**
		 * 返回的字符串
		 */
		String[] data = null;
		try {
			// a|b|C|d
			// vector性能很低,用System.arraycopy来代替vector;上网查System.arraycopy的使用方法和优点
			// 放的是字符chr的位�?
			Vector<Integer> vector = new Vector<Integer>();
			for (int i = 0; i < str.length(); i++) {
				char c = str.charAt(i);
				if (chr == c) {
					// i是字符的位置
					vector.addElement(new Integer(i));
				}
			}

			// 字符串中没有要切割的字符
			if (vector.size() == 0) {
				data = new String[] { str };
			}

			if (vector.size() >= 1) {
				data = new String[vector.size() + 1];
			}
			for (int i = 0; i < vector.size(); i++) {
				/**
				 * 位置
				 */
				int index = ((Integer) vector.elementAt(i)).intValue();
				String temp = "";
				if (i == 0)// 第一�?
				{
					if (vector.size() == 1) {
						temp = str.substring(index + 1);
						data[1] = temp;
					}
					temp = str.substring(0, index);
					data[0] = temp;
				} else if (i == vector.size() - 1)// //�?���?��#
				{
					int preIndex = ((Integer) vector.elementAt(i - 1))
							.intValue();
					temp = str.substring(preIndex + 1, index);// �?���?��#前面的内�?
					data[i] = temp;
					temp = str.substring(index + 1);// �?���?��#后面的内�?
					data[i + 1] = temp;
				} else {
					int preIndex = ((Integer) vector.elementAt(i - 1))
							.intValue();
					temp = str.substring(preIndex + 1, index);// �?���?��#前面的内�?
					data[i] = temp;
				}
			}

		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			return data;
		}
	}

	public static String converPhone(String phone) {
		return phone.substring(0, 3) + "****" + phone.substring(7);
	}

	public static String maskPhone(String phone) {
		StringBuffer sb = new StringBuffer();
		int a = 0;
		for (int i = 0; i < phone.length(); i++) {
			a = Integer.parseInt(phone.substring(i, i + 1)) + 6;
			if (a >= 10) {
				switch (a) {
				case 10:
					sb.append("a");
					break;
				case 11:
					sb.append("b");
					break;
				case 12:
					sb.append("c");
					break;
				case 13:
					sb.append("d");
					break;
				case 14:
					sb.append("e");
					break;
				case 15:
					sb.append("f");
					break;
				}
			} else {
				sb.append(a);
			}
		}
		return sb.toString();
	}

	public static String deMaskPhone(String str) {
		StringBuffer sb = new StringBuffer();
		String a = "";
		int b = 0;
		for (int i = 0; i < str.length(); i++) {
			a = str.substring(i, i + 1);

			try {
				b = Integer.parseInt(a);
				sb.append(b - 6);
			} catch (NumberFormatException e) {
				// TODO Auto-generated catch block
				// e.printStackTrace();
				if (a.equals("a"))
					sb.append(10 - 6);
				else if (a.equals("b"))
					sb.append(11 - 6);
				else if (a.equals("c"))
					sb.append(12 - 6);
				else if (a.equals("d"))
					sb.append(13 - 6);
				else if (a.equals("e"))
					sb.append(14 - 6);
				else if (a.equals("f"))
					sb.append(15 - 6);

			}

		}
		return sb.toString();
	}

	public static void main(String args[]) {
		String str = maskPhone("13552016488");
//		System.out.println(str);
		str = deMaskPhone(str);
//		System.out.println(str);
	}

	// public static boolean checkNetworkConnection(Context context) {
	// final ConnectivityManager connMgr = (ConnectivityManager) context
	// .getSystemService(Context.CONNECTIVITY_SERVICE);
	//
	// final NetworkInfo wifi = connMgr
	// .getNetworkInfo(ConnectivityManager.TYPE_WIFI);
	// final NetworkInfo mobile = connMgr
	// .getNetworkInfo(ConnectivityManager.TYPE_MOBILE);
	//
	// if(wifi.isAvailable()){
	// }
	// if(mobile.isAvailable()){
	// }
	// if (wifi.isAvailable() || mobile.isAvailable())
	// return true;
	// else
	// return false;
	// }

}
