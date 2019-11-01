package com.market.hjapp.network;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.text.TextUtils;
import android.text.format.DateFormat;
import android.util.Log;

import com.market.hjapp.App;
import com.market.hjapp.Category;
import com.market.hjapp.ChargeChannel;
import com.market.hjapp.Comment;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.Recommend;
import com.market.hjapp.SecurityUtil;
import com.market.hjapp.UserInfo;

import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.InetSocketAddress;
import java.net.URL;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

public class HTTParser {

	private static final String TAG = "HTTParser";

	protected static HashMap<String, Object> getAppInfoList(Context ctx,
			String applist) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiveParams(ctx, applist);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_APPINFO_LIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_APPINFO_LIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_APPINFO_LIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {
					resp.put("list",
							getAppListByJSON(jsonResp.getJSONArray("list")));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoFiveParams(Context ctx, String applist)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();

		addBasicParams(params, 5, ctx);

		params.putString("applist", applist);

		return params;
	}

	private static Bundle getProtoNighteenParams(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();

		addBasicParams(params, 19, ctx);

		return params;
	}

	public static HashMap<String, Object> anonymousLogin(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoOneParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_ANONYMOUS_LOGIN, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_ANONYMOUS_LOGIN");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_ANONYMOUS_LOGIN, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {

				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					resp.put("mid", Uri.decode(jsonResp.getString("mid")));
					resp.put("sid", Uri.decode(jsonResp.getString("sid")));
					resp.put("user_guide", jsonResp.getInt("user_guide"));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	public static HashMap<String, Object> getCategoryList(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyOneParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_DAILY_DATA, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_DAILY_DATA");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_DAILY_DATA, paramsPair);

		if (requestSuccess(response)) {
			// JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			//
			// if (jsonResp != null) {
			//
			// resp.put("proto", jsonResp.getInt("proto"));
			//
			// boolean reqStatus = jsonResp.getBoolean("reqsuccess");
			// resp.put("reqsuccess", reqStatus);
			//
			// if (reqStatus) {
			// JSONArray cateArray = jsonResp.getJSONArray("list");
			// ArrayList<Category> cateList = null;
			// if (cateArray != null) {
			// cateList = new ArrayList<Category>();
			// int size = cateArray.length();
			//
			// for (int i = 0; i < size; i++) {
			// Category c = new Category();
			//
			// JSONObject jo = cateArray.getJSONObject(i);
			//
			// c.setParentId(Uri.decode(jo.getString("parent")));
			// c.setName(Uri.decode(jo.getString("name")));
			// c.setIconUrl(Uri.decode(jo.getString("icon")));
			// c.setSig(Uri.decode(jo.getString("sig")));
			// c.setAppCount(jo.getInt("appcnt"));
			//
			// c.setIsChart(jo.getBoolean("is_chart"));
			//
			// c.setNewFreeAppList(jo.getString("time_applist"));
			//
			// c.setNewAppName(Uri.decode(jo
			// .getString("time_appname")));
			//
			// if (!c.getIsChart()) {
			// c.setHotFreeAppList(jo
			// .getString("down_applist"));
			// }
			//
			// c.setUpdateInterval(jo.getInt("update_interval"));
			// GeneralUtil.saveCateUpdateInterval(ctx, c.getSig(),
			// c.getUpdateInterval());
			//
			// // special for RECENT DOWNLOAD CATE
			// if (c.getSig().equals(
			// ConstantValues.SUGGESTED_CATE_IDLIST[2])) {
			// GeneralUtil.saveDownloadTime(ctx, jo
			// .getString("downtime_list"));
			// }
			//
			// if (c.getIsChart()) {
			// MyLog.d(TAG, "cate id:" + c.getSig());
			// c.setDescription(jo.getString("desc"));
			// }
			//
			// cateList.add(c);
			//
			// }
			// }
			//
			// resp.put("list", cateList);
			//
			// resp.put("hotwords", jsonResp.getString("hotwords"));
			//
			// boolean call_back = (Boolean) jsonResp
			// .getBoolean("callback");
			// int call_back_time = (Integer) jsonResp
			// .getInt("callback_time");
			//
			// GeneralUtil.saveNeedCallBackToday(ctx, call_back);
			// GeneralUtil.saveCallBackTime(ctx, call_back_time);
			//
			// MyLog.d(TAG, "call back:" + call_back);
			// MyLog.d(TAG, "call_back_time:" + call_back_time);
			//
			// JSONArray rankArray = jsonResp.getJSONArray("rank");
			// ArrayList<Category> rankList = null;
			// if (rankArray != null) {
			// rankList = new ArrayList<Category>();
			// int size = rankArray.length();
			//
			// for (int i = 0; i < size; i++) {
			// Category c = new Category();
			//
			// JSONObject jo = rankArray.getJSONObject(i);
			//
			// c.setName(Uri.decode(jo.getString("name")));
			// c.setIconUrl(Uri.decode(jo.getString("icon")));
			// c.setSig(Uri.decode(jo.getString("sig")));
			// c.setRankAppListInWeek(jo.getString("week"));
			// c.setRankAppListInMonth(jo.getString("month"));
			// c.setRankAppListInAll(jo.getString("all"));
			// c.setDescription(jo.getString("desc"));
			// rankList.add(c);
			//
			// }
			// }
			//
			// resp.put("rank", rankList);
			//
			// } else {
			// storeErrorMsg(resp, jsonResp, ctx);
			// }
			// }
		}

		return resp;
	}

	public static HashMap<String, Object> downloadApp(Context ctx,
			String appid, String payid, String source)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoElevenParams(ctx, appid, payid, source);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_DOWNLOAD, paramsPair);
		// } catch (Exception e) {
		MyLog.d(TAG,"URL_DOWNLOAD");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_DOWNLOAD, paramsPair);
		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					resp.put("location", jsonResp.getString("location"));
					resp.put("download_id", jsonResp.getString("download_id"));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	public static HashMap<String, Object> register(Context ctx,
			String username, String password, String nickname)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoSeventeenParams(ctx, username, password,
				nickname);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_REGISTER, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_REGISTER");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_REGISTER, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {

					UserInfo user = new UserInfo();
					user.setBalance("0");
					user.setPhone("");
					user.setEmail(username);
					user.setName(nickname);

					GeneralUtil.saveLoggedIn(user, ctx);

					GeneralUtil.saveUid(ctx, jsonResp.getString("uid"));
					GeneralUtil.saveSid(ctx, jsonResp.getString("sid"));

				}
			}
		}
		return resp;
	}

	public static HashMap<String, Object> login(Context ctx, String username,
			String password, String name) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoEighteenParams(ctx, username, password, name);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_LOGIN, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_LOGIN");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_LOGIN, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					UserInfo user = new UserInfo();
					user.setBalance(jsonResp.getString("balance"));
					user.setPhone(jsonResp.getString("phone"));
					user.setEmail(jsonResp.getString("email"));
					user.setName(jsonResp.getString("name"));
					GeneralUtil.saveLoggedIn(user, ctx);

					GeneralUtil.saveUid(ctx, jsonResp.getString("uid"));
					GeneralUtil.saveSid(ctx, jsonResp.getString("sid"));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	public static HashMap<String, Object> setUserInfo(Context ctx,
			String phone, String name, String type)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyOneParams(ctx, phone, name, type);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SET_USERINFO, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_SET_USERINFO");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_SET_USERINFO, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}
		return resp;
	}
	
	public static HashMap<String, Object> setPhone(Context ctx,
			String phone, String validate)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiftySevenParams(ctx, phone,validate);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SET_USERINFO, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("setPhone");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_BIND_PHONE, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}
		return resp;
	}
	public static HashMap<String, Object> SendVerify(Context ctx,
			String phone)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiftySixParams(ctx, phone);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SET_USERINFO, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("SendVerify");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_SEND_VERIFY, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}
		return resp;
	}
	public static HashMap<String, Object> getScreenshotList(Context ctx,
			String appid) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoSevenParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_SCREENSHOT_LIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_SCREENSHOT_LIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_SCREENSHOT_LIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					JSONArray appArray = jsonResp.getJSONArray("list");

					ArrayList<String> screenshotList = new ArrayList<String>();

					if (appArray != null) {
						int size = appArray.length();

						for (int i = 0; i < size; i++) {

							JSONObject jo = appArray.getJSONObject(i);

							screenshotList.add(Uri.decode(jo.getString("url")));
						}
					}

					resp.put("list", screenshotList);
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	public static HashMap<String, Object> logout(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoNighteenParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_LOGOUT, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_LOGOUT");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_LOGOUT, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					resp.put("sid", Uri.decode(jsonResp.getString("sid")));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	// private static boolean requestSuccess(HttpResponse response) {
	// return (response != null)
	// && (response.getStatusLine().getStatusCode() == 200);
	// }
	private static boolean requestSuccess(String response) {
		return (response != null) && (!"".equals(response));
	}

	private static void addBasicParams(Bundle params, int protoNum, Context ctx) {
		params.putString("proto", Integer.toString(protoNum));
		params.putString("mid", GeneralUtil.getMid(ctx));
		params.putString("uid", GeneralUtil.getUid(ctx));
		params.putString("sid", GeneralUtil.getSid(ctx));
		params.putString("screen_size", GeneralUtil.getScreenSize(ctx));
		params.putString("sdk", GeneralUtil.getSDK());
	}

	private static Bundle getProtoOneParams(Context ctx) {
		Bundle params = new Bundle();
		params.putString("proto", "1");
		params.putString("imei", GeneralUtil.getIMEI(ctx));
		params.putString("imsi", GeneralUtil.getIMSI(ctx));
		params.putString("ver", ConstantValues.CLIENT_VERSION_NUMBER + "");
		params.putString("model", ConstantValues.DEVICE_ID_CURRENT + "");
		params.putString("channel", ConstantValues.CHANNEL_ID_CURRENT + "");

		params.putString("screen_size", GeneralUtil.getScreenSize(ctx));
		params.putString("board", GeneralUtil.getBOARD());
		params.putString("brand", GeneralUtil.getBRAND());
		params.putString("cpu_abi", GeneralUtil.getCPU_ABI());
		params.putString("device", GeneralUtil.getDEVICE());
		params.putString("diplay", GeneralUtil.getDISPLAY());
		params.putString("fingerprint", GeneralUtil.getFINGERPRINT());
		params.putString("host", GeneralUtil.getHOST());
		params.putString("build_id", GeneralUtil.getBuildID());
		params.putString("manufacturer", GeneralUtil.getMANUFACTURER());
		params.putString("model_name", GeneralUtil.getMODEL());
		params.putString("product", GeneralUtil.getPRODUCT());
		params.putString("tags", GeneralUtil.getTAGS());
		params.putString("time", GeneralUtil.getTIME());
		params.putString("time", GeneralUtil.getTIME());
		params.putString("type", GeneralUtil.getTYPE());
		params.putString("user", GeneralUtil.getUSER());
		params.putString("codename", GeneralUtil.getCODENAME());
		params.putString("incremental", GeneralUtil.getINCREMENTAL());
		params.putString("release", GeneralUtil.getRELEASE());
		params.putString("sdk", GeneralUtil.getSDK());
		params.putString("sdk_int", GeneralUtil.getSDK_INT());

		return params;
	}

	// private static Bundle getProtoThreeParams(Context ctx, String cateId,
	// boolean isChart, String sorting, int pageno) {
	// Bundle params = new Bundle();
	//
	// addBasicParams(params, 3, ctx);
	//
	// params.putString("cate", cateId);
	// params.putString("is_chart", Boolean.toString(isChart));
	//
	// if (pageno == -1) {
	// // doesn't support paging
	// params.putString("has_paging", "no");
	// } else {
	// params.putString("has_paging", "yes");
	// params.putString("num_per_page", ConstantValues.NUM_PER_PAGE);
	// params.putString("pageno", pageno + "");
	// }
	//
	// if (sorting == null) {
	// // doesn't support sorting
	// params.putString("has_sorting", "no");
	// } else {
	// params.putString("has_sorting", "yes");
	// params.putString("sort", sorting);
	// }
	//
	// return params;
	// }

	private static Bundle getProtoSevenParams(Context ctx, String appid) {
		Bundle params = new Bundle();

		addBasicParams(params, 7, ctx);

		params.putString("appid", appid);

		return params;
	}

	private static Bundle getProtoSeventeenParams(Context ctx, String username,
			String password, String nickname) {
		Bundle params = new Bundle();

		addBasicParams(params, 17, ctx);

		params.putString("email", username);
		params.putString("name", nickname);
		params.putString("passwd",
				encryptPassword(password, GeneralUtil.getSid(ctx)));
		params.putString("imei", GeneralUtil.getIMEI(ctx));
		params.putString("imsi", GeneralUtil.getIMSI(ctx));
		params.putString("ver", ConstantValues.CLIENT_VERSION_NUMBER + "");
		params.putString("model", ConstantValues.DEVICE_ID_CURRENT + "");
		params.putString("channel", ConstantValues.CHANNEL_ID_CURRENT + "");

		return params;
	}

	private static Bundle getProtoEighteenParams(Context ctx, String username,
			String password, String name) {
		Bundle params = new Bundle();

		addBasicParams(params, 18, ctx);

		if (GeneralUtil.isMobileNO(username)) {
			if (name.equals("")) {
				params.putString("type", "1");
				params.putString("email", "");
				params.putString("phone", username);
			} else {
				params.putString("type", "3");
				params.putString("email", "");
				params.putString("phone", username);
				params.putString("name", name);
			}

		} else {
			if (name.equals("")) {
				params.putString("type", "0");
				params.putString("phone", "");
				params.putString("email", username);
			} else {
				params.putString("type", "2");
				params.putString("phone", "");
				params.putString("email", username);
				params.putString("name", name);
			}
		}

		params.putString("passwd",
				encryptPassword(password, GeneralUtil.getSid(ctx)));

		return params;
	}

	private static Bundle getProtoTwentyOneParams(Context ctx, String phone,
			String name, String type) {
		Bundle params = new Bundle();

		addBasicParams(params, 21, ctx);

		params.putString("phone", phone);
		params.putString("name", name);
		params.putString("type", type);
		return params;
	}
	private static Bundle getProtoFiftySixParams(Context ctx, String phone) {
		Bundle params = new Bundle();

		addBasicParams(params, 56, ctx);

		params.putString("phone", phone);
		return params;
	}
	private static Bundle getProtoFiftySevenParams(Context ctx, String phone,String validate) {
		Bundle params = new Bundle();

		addBasicParams(params, 57, ctx);

		params.putString("phone", phone);
		params.putString("verify", validate);
		return params;
	}
	
	static final String HEXES = "0123456789abcdef";

	private static String encryptPassword(String passwd, String sid) {
		MyLog.d(TAG, "passwd: " + passwd + ", sid: " + sid);
		try {
			MessageDigest digest = MessageDigest
					.getInstance("MD5");
			digest.update(passwd.getBytes());

			byte[] p = digest.digest();
			int j;
			StringBuffer buf1 = new StringBuffer("");
			for (int offset = 0; offset < p.length; offset++) {
				j = p[offset];
				if (j < 0)
					j += 256;
				if (j < 16)
					buf1.append("0");
				buf1.append(Integer.toHexString(j));
			}

			byte[] pp = buf1.toString().getBytes();
			MyLog.d(TAG, "P -> " + buf1.toString());

			return buf1.toString();

			// float md = (float) pp.length;
			// float sf = (float) sid.length();
			//
			// int t = (int) Math.ceil(md / sf);
			// MyLog.d(TAG, "md: " + md + ", sf: " + sf + ", T -> " + t);
			//
			// StringBuilder buf = new StringBuilder();
			// for (int i = 0; i < t; i++) {
			// buf.append(sid);
			// }
			//
			// byte[] s = buf.toString().getBytes();
			// byte[] r = new byte[pp.length];
			//
			// MyLog.d(TAG, "S -> " + s);
			//
			// for (int i = 0; i < pp.length; i++) {
			// r[i] = (byte) (pp[i] ^ s[i]);
			// }
			//
			// final StringBuilder hex = new StringBuilder(2 * r.length);
			// for (final byte b : r) {
			// hex.append(HEXES.charAt((b & 0xf0) >> 4)).append(
			// HEXES.charAt((b & 0x0f)));
			// }
			// return hex.toString();
		} catch (NoSuchAlgorithmException e) {
			MyLog.e(TAG, "", e);
		}
		return null;
	}

	private static void storeErrorMsg(HashMap<String, Object> map,
			JSONObject jsonObject, Context ctx) throws JSONException,
			ClientProtocolException, IOException {
		MyLog.d(TAG, "in error function");
		String errNo = Uri.decode(jsonObject.getString("errno"));
		String errMsg = Uri.decode(jsonObject.getString("errmsg"));
		map.put("errno", errNo);
		MyLog.d(TAG, "errno: " + errNo);
		map.put("errmsg", errMsg);
		MyLog.d(TAG, "errmsg: " + errMsg);
	}

	public static HashMap<String, Object> pay(Context ctx, String parentId,
			String appid, String price, String passwd)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();
		Bundle params = getProtoFourteenParams(ctx, parentId, appid, price,
				passwd);

		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_BUY, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_BUY");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_BUY, paramsPair);

		if (requestSuccess(response)) {
			String result = response;// response.toString();
			MyLog.d(TAG, "HTTP " + result);

			// use DES decode result
			result = SecurityUtil.getDESDecrypt(result);

			MyLog.d(TAG, "HTTP Decrypt" + result);

			JSONObject jsonResp = HTTPUtil.getJsonObject(result, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));
				resp.put("reqsuccess", jsonResp.getBoolean("reqsuccess"));

				if (!jsonResp.getBoolean("reqsuccess")) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					resp.put("payid", jsonResp.getInt("payid"));
					resp.put("balance", jsonResp.getString("balance"));
				}
			}
		} else {
		}

		return resp;
	}

	private static Bundle getProtoFourteenParams(Context ctx, String parentId,
			String appid, String price, String passwd)
			throws ClientProtocolException, IOException, JSONException {
		Bundle encryptParams = new Bundle();

		MyLog.d(TAG, " parentId:" + parentId);
		MyLog.d(TAG, " appid:" + appid);

		// generator DES key
		String DESkey = SecurityUtil.generatorDESRandomKey();
		MyLog.d(TAG, " DESkey:" + DESkey);

		// use RSA encrypt DES key
		String keyEncode = SecurityUtil.getMARKETRSAEncrypt(DESkey);
		MyLog.d(TAG, " keyEncode:" + keyEncode);

		String params = 14 + "|" + GeneralUtil.getUid(ctx) + "|"
				+ GeneralUtil.getSid(ctx) + "|" + parentId + "|" + appid + "|"
				+ passwd + "|" + price;

		// use DES encrypt parameters
		String stringEncode = SecurityUtil.getDESEncrypt(params);
		MyLog.d(TAG, " stringEncode:" + stringEncode);

		addBasicParams(encryptParams, 14, ctx);
		encryptParams.putString("keyEncode", keyEncode);
		encryptParams.putString("stringEncode", stringEncode);

		return encryptParams;
	}

	public static HashMap<String, Object> charge(Context ctx, String aid,
			String amount, String limit, String serial, String passwd)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();
		Bundle params = getProtoSixteenParams(ctx, aid, amount, limit, serial,
				passwd);

		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_CHARGE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_CHARGE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_CHARGE, paramsPair);

		if (requestSuccess(response)) {
			String result = response;// .toString();
			MyLog.d(TAG, "charge response " + result);

			// use DES decode result
			result = SecurityUtil.getDESDecrypt(result);
			MyLog.d(TAG, "charge response Decrypt " + result);

			if (result == null) {
				resp.put("proto", 14);
				resp.put("reqsuccess", false);
				resp.put("errno", "E899");
				resp.put("errno", "Des Decrypt Error");
			} else {
				JSONObject jsonResp = HTTPUtil.getJsonObject(result, false);

				if (jsonResp != null) {
					resp.put("proto", jsonResp.getInt("proto"));

					boolean reqStatus = jsonResp.getBoolean("reqsuccess");
					resp.put("reqsuccess", reqStatus);

					if (!reqStatus) {
						storeErrorMsg(resp, jsonResp, ctx);
					} else {
						resp.put("balance", jsonResp.getString("balance"));
					}
				}
			}
		} else {
		}

		return resp;
	}

	private static Bundle getProtoSixteenParams(Context ctx, String aid,
			String amount, String limit, String serial, String passwd)
			throws ClientProtocolException, IOException, JSONException {
		Bundle encryptParams = new Bundle();

		// generator DES key
		String DESkey = SecurityUtil.generatorDESRandomKey();
		MyLog.d(TAG, " DESkey:" + DESkey);

		// use RSA encrypt DES key
		String keyEncode = SecurityUtil.getMARKETRSAEncrypt(DESkey);
		MyLog.d(TAG, " keyEncode:" + keyEncode);

		String params = "16" + "|" + GeneralUtil.getUid(ctx) + "|"
				+ GeneralUtil.getSid(ctx) + "|" + amount + "|" + limit + "|"
				+ serial + "|" + passwd + "|" + aid;

		// use DES encrypt parameters
		String stringEncode = SecurityUtil.getDESEncrypt(params);
		MyLog.d(TAG, " stringEncode:" + stringEncode);

		addBasicParams(encryptParams, 16, ctx);
		encryptParams.putString("keyEncode", keyEncode);
		encryptParams.putString("stringEncode", stringEncode);

		return encryptParams;
	}

	public static HashMap<String, Object> getChargeList(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyThreeParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_CHARGE_LIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_CHARGE_LIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_CHARGE_LIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					JSONArray chargeListJSON = jsonResp.getJSONArray("list");

					ArrayList<ChargeChannel> chargeList = new ArrayList<ChargeChannel>();

					int size = chargeListJSON.length();

					for (int i = 0; i < size; i++) {
						ChargeChannel c = new ChargeChannel();

						JSONObject jo = chargeListJSON.getJSONObject(i);

						c.setName(Uri.decode(jo.getString("cardName")));
						c.setId(jo.getString("cardID"));
						c.setIconUrl(jo.getString("cardIcon"));

						chargeList.add(c);
					}

					resp.put("list", chargeList);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyThreeParams(Context ctx) {
		Bundle params = new Bundle();

		addBasicParams(params, 23, ctx);

		return params;
	}

	public static HashMap<String, Object> findPassword(Context ctx,
			String userEmail) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyFourParams(ctx, userEmail);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);

		MyLog.d(TAG, "email=" + userEmail);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_FIND_PASSWORD, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_FIND_PASSWORD");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_FIND_PASSWORD, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyFourParams(Context ctx, String email) {
		Bundle params = new Bundle();

		addBasicParams(params, 24, ctx);
		params.putString("email", email);

		return params;
	}

	public static HashMap<String, Object> search(Context ctx, String word)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle protoFourParams = getProtoNineParams(ctx, word);
		List<NameValuePair> paramsPair = HTTPUtil
				.getPostRequestParams(protoFourParams);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SEARCH, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_SEARCH");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_SEARCH, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					resp.put("pageno", jsonResp.getInt("pageno"));
					resp.put("totalpage", jsonResp.getInt("totalpage"));

					MyLog.d(TAG, "search,get app list>>> status: " + reqStatus);
					if (reqStatus) {
						resp.put("list",
								getAppListByJSON(jsonResp.getJSONArray("list")));
					}
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoNineParams(Context ctx, String word)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();

		addBasicParams(params, 9, ctx);

		params.putString("word", word);
		return params;
	}

	public static HashMap<String, Object> changePassword(Context ctx,
			String oldPassword, String newPassword)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyParams(ctx, oldPassword, newPassword);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_CHANGE_PASSWORD, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_CHANGE_PASSWORD");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_CHANGE_PASSWORD, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyParams(Context ctx, String oldPassword,
			String newPassword) {
		Bundle params = new Bundle();

		addBasicParams(params, 20, ctx);

		params.putString("old_passwd",
				encryptPassword(oldPassword, GeneralUtil.getSid(ctx)));
		params.putString("new_passwd",
				encryptPassword(newPassword, GeneralUtil.getSid(ctx)));

		return params;
	}

	/**
	 * 获得夺宝的下载地址
	 * @param ctx
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	public static HashMap<String, Object> getDuoBaoUrl(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();
		
		Bundle params = getDuoBaoUrlParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_UPGRADE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("GET_DUOBAO_URL");
		// }
		String url = "http://www.hjapk.com/UserCenter/index.php?m=Active&a=getVersion";
		String response = HTTPUtil.getHttpPostResponse(ctx,url, paramsPair);
		MyLog.e("获得的响应为： ", " "+response);
		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			
			if (jsonResp != null) {
				resp.put("reqsuccess", true);
				resp.put("version", jsonResp.opt("version"));
				MyLog.e("获得的响应为ad： ", " version: "+jsonResp.opt("version"));
				resp.put("url", jsonResp.opt("url"));
				MyLog.e("获得的响应为adad： ", " url: "+jsonResp.opt("url"));
			}
		}
		
		return resp;
	}
	public static HashMap<String, Object> getSelfVerUpgrade(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwoParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_UPGRADE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_UPGRADE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_UPGRADE, paramsPair);
		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));
				boolean reqStatus = jsonResp.getBoolean("reqsuccess");

				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {

					resp.put(
							"version",
							new Integer(Uri.decode(jsonResp
									.getString("cur_ver"))));
					resp.put("url", Uri.decode(jsonResp.getString("loc")));
					resp.put("app_version",
							Uri.decode(jsonResp.getString("app_version")));
					resp.put("changelog",
							Uri.decode(jsonResp.getString("changelog")));
					resp.put("need_upgrade",
							jsonResp.getBoolean("need_upgrade"));

				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	/**
	 * 临时使用的 参数无效
	 * @param ctx
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	private static Bundle getDuoBaoUrlParams(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();
		addBasicParams(params, 100, ctx);
		params.putString("ver", "" + ConstantValues.CLIENT_VERSION_NUMBER);
		return params;
	}
	private static Bundle getProtoTwoParams(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();
		addBasicParams(params, 2, ctx);
		params.putString("ver", "" + ConstantValues.CLIENT_VERSION_NUMBER);
		return params;
	}

	public static HashMap<String, Object> getComments(Context ctx, String appid)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoEightParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_COMMENTS, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_COMMENTS");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_COMMENTS, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					JSONArray commentsArray = jsonResp.getJSONArray("list");

					ArrayList<Comment> commentList = new ArrayList<Comment>();

					int size = commentsArray.length();

					for (int i = 0; i < size; i++) {
						Comment c = new Comment();

						JSONObject jo = commentsArray.getJSONObject(i);

						c.setName(Uri.decode(jo.getString("name")));
						c.setTime(Uri.decode(jo.getString("time")));
						// c.setRate(Uri.decode(jo.getString("rate")));
						c.setRate(jo.getString("score"));
						// c.setContent(Uri.decode(jo.getString("comment")));
						// remove Uri.decode() in order to solve the problem of
						// garbled %
						c.setContent(jo.getString("comment"));
						commentList.add(c);
					}

					resp.put("list", commentList);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoEightParams(Context ctx, String appid) {
		Bundle params = new Bundle();

		addBasicParams(params, 8, ctx);

		params.putString("appid", appid);

		return params;
	}

	public static HashMap<String, Object> comment(Context ctx, String appid,
			String rating, String content) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirteenParams(ctx, appid, rating, content);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_COMMENT, paramsPair);
		// } catch (Exception e) {
		MyLog.i("URL_COMMENT"," ");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_COMMENT, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					resp.put("appid", appid);
					resp.put("total_score",
							Integer.parseInt(jsonResp.getString("total_score")));
					resp.put("total_num",
							Integer.parseInt(jsonResp.getString("total_num")));
					resp.put("total_comment_num", Integer.parseInt(jsonResp
							.getString("total_rate_num")));
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoThirteenParams(Context ctx, String appid,
			String rating, String content) {
		Bundle params = new Bundle();

		addBasicParams(params, 13, ctx);

		params.putString("appid", appid);
		params.putString("score", rating);
		params.putString("comment", content);

		return params;
	}

	public static HashMap<String, Object> score(Context ctx, String appid,
			String score) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyTwoParams(ctx, appid, score);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_COMMENT_SCORE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_COMMENT_SCORE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_COMMENT_SCORE, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					resp.put("appid", appid);
					resp.put("total_score",
							Integer.parseInt(jsonResp.getString("total_score")));
					resp.put("total_num",
							Integer.parseInt(jsonResp.getString("total_num")));
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoFortyTwoParams(Context ctx, String appid,
			String score) {
		Bundle params = new Bundle();

		addBasicParams(params, 42, ctx);

		params.putString("appid", appid);
		params.putString("score", score);
		return params;
	}

	public static HashMap<String, Object> getMyRating(Context ctx, String appid)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentySevenParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_MY_RATING, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_MY_RATING");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_MY_RATING, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				} else {
					resp.put("score",
							Integer.parseInt(jsonResp.getString("score")));
				}
			}
		}

		return resp;
	}

	protected static HashMap<String, Object> postDownloadSuccess(Context ctx,
			String pid, String downloadId) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle protoElevenParams = getProtoTwelveParams(ctx, pid, downloadId);
		List<NameValuePair> paramsPair = HTTPUtil
				.getPostRequestParams(protoElevenParams);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_UPDATE_DOWNLOAD_LOG, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_UPDATE_DOWNLOAD_LOG");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_UPDATE_DOWNLOAD_LOG, paramsPair);
		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentySevenParams(Context ctx, String appid) {
		Bundle params = new Bundle();

		addBasicParams(params, 27, ctx);

		params.putString("appid", appid);

		return params;
	}

	/**
	 * Check if the current connection is CMWAP
	 * 
	 * @param ctx
	 *            the context.
	 * @return Returns true for CMWAP,false for others
	 * @author zxg
	 * @data 20120331
	 */
	public static boolean isCMWAP(Context ctx) {

		String currentAPN = "";
		ConnectivityManager conManager = ((ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE));

		NetworkInfo info = conManager
				.getNetworkInfo(ConnectivityManager.TYPE_MOBILE);
		currentAPN = info.getExtraInfo();
		Log.d("网络接入方式", "网络接入方式: "+currentAPN);
		if (currentAPN == null || currentAPN == "") {
			return false;
		} else {
			if (currentAPN.equals("cmwap")) {
				return true;
			} else {
				return false;
			}

		}
	}
	/**
	 * 
	 * @param ctx
	 * @return returns true for CTWAP,false for others
	 */
	public static boolean isCTWAP(Context ctx) {
		
		String currentAPN = "";
		ConnectivityManager conManager = ((ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE));
		
		NetworkInfo info = conManager
				.getNetworkInfo(ConnectivityManager.TYPE_MOBILE);
		currentAPN = info.getExtraInfo();
		Log.d("网络接入方式", "网络接入方式: "+currentAPN);
		if (currentAPN == null || currentAPN == "") {
			return false;
		} else {
			if (currentAPN.equals("CTC")) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	private static final int TIMEOUT = 30 * 1000;

	public static HashMap<String, Object> imageLoader(Context ctx,
			String imageUrl) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();
		if (TextUtils.isEmpty(imageUrl)) {
			return null;
		}

		HttpURLConnection urlConn = null;
		InputStream is = null;
		BufferedInputStream bufferedInputStream = null;
		// String proxyHost = android.net.Proxy.getDefaultHost();
		// if (proxyHost != null) {
		// MyLog.d(TAG, "proxyHost:"+proxyHost);
		// java.net.Proxy p = new java.net.Proxy(java.net.Proxy.Type.HTTP,
		// new InetSocketAddress(android.net.Proxy.getDefaultHost(),
		// android.net.Proxy.getDefaultPort()));
		//
		// urlConn = (HttpURLConnection) new URL(imageUrl).openConnection(p);
		//
		// } else {
		// urlConn =(HttpURLConnection) new URL(imageUrl).openConnection();
		//
		// }
		NetworkInfo info = ((ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE))
				.getActiveNetworkInfo();

		if (info == null)
			return null;

		MyLog.e(TAG, "info.getType(): "+info.getType()+" (TYPE_MOBILE      = 0;TYPE_WIFI        = 1;TYPE_MOBILE_MMS  = 2;TYPE_MOBILE_SUPL = 3;TYPE_MOBILE_DUN  = 4;TYPE_MOBILE_HIPRI = 5; TYPE_WIMAX = 6;)");
		if (info.getType() != ConnectivityManager.TYPE_WIFI) {
			String proxyHost = android.net.Proxy.getDefaultHost();
			MyLog.e(TAG, "proxyHost: "+proxyHost);
			// 20120331,zxg,fix cmwap can't connect.
			if (isCMWAP(ctx)) {
				MyLog.e(TAG, "isCMWAP");

				int contentBeginIdx = imageUrl.indexOf('/', 7);
				StringBuffer urlStringBuffer = new StringBuffer(
						"http://10.0.0.172:80");

				urlStringBuffer.append(imageUrl.substring(contentBeginIdx));
				urlConn = (HttpURLConnection) new URL(
						urlStringBuffer.toString()).openConnection();
				urlConn.setRequestProperty("X-Online-Host",
						imageUrl.substring(7, contentBeginIdx));

			}else if(isCTWAP(ctx)){
				MyLog.e(TAG, "isCTWAP");
				java.net.Proxy proxy = new java.net.Proxy(java.net.Proxy.Type.HTTP, new InetSocketAddress("10.0.0.200", 80));   
				urlConn = (HttpURLConnection) new URL(imageUrl).openConnection(proxy);
			}else if (proxyHost != null) {
				MyLog.d(TAG, "proxyHost:" + proxyHost);
				java.net.Proxy p = new java.net.Proxy(java.net.Proxy.Type.HTTP,
						new InetSocketAddress(
								android.net.Proxy.getDefaultHost(),
								android.net.Proxy.getDefaultPort()));

				urlConn = (HttpURLConnection) new URL(imageUrl)
						.openConnection(p);

			} else {
				MyLog.e(TAG, "Other proxy.");
				urlConn = (HttpURLConnection) new URL(imageUrl)
						.openConnection();

			}
		} else
			urlConn = (HttpURLConnection) new URL(imageUrl).openConnection();

		// urlConn.setRequestMethod("GET");
		urlConn.setDoInput(true);
		// urlConn.setDoOutput(true);
		// urlConn.setUseCaches(false);
		urlConn.setInstanceFollowRedirects(true);
		urlConn.setConnectTimeout(TIMEOUT);
		urlConn.connect();

		int responseCode = urlConn.getResponseCode();
		MyLog.d(TAG, "responseCode=" + responseCode);
		if (HttpURLConnection.HTTP_OK == responseCode) {
			try {
				is = urlConn.getInputStream();
				bufferedInputStream = new BufferedInputStream(is);

				Bitmap bitmap = null;

				System.gc();
				/**
				 * 报 OOM解决方法： 1、调整merory useage mOptions.inSampleSize =
				 * 2;//return an image that is 1/2 the width/height of the
				 * original, and 1/4 the number of pixels.
				 * 
				 * 2、调整bitmap size bitmap = Bitmap.createScaledBitmap(bitmap,
				 * 100, 150, false);
				 * 
				 * 3、调整temp storage mOptions.inSampleSize = new byte[100 *
				 * 1024]; 参考：http://stackoverflow.com/questio ...
				 * out-of-memory-issue
				 */
//				BitmapFactory.Options options = new BitmapFactory.Options();
//				options.inSampleSize = 2;
//				options.inTempStorage = new byte[100 * 1024];
				// options.inJustDecodeBounds = true;
//				bitmap = BitmapFactory.decodeStream(bufferedInputStream, null,
//						options);
				bitmap = BitmapFactory.decodeStream(bufferedInputStream);

				resp.put("reqsuccess", true);
				resp.put("context", ctx);
				resp.put("imageUrl", imageUrl);
				resp.put("bitmap", bitmap);

			} catch (Exception e) {
				MyLog.e(TAG, "fetchDrawable failed", e);

				if (bufferedInputStream != null) {
					bufferedInputStream.close();
				}

				if (is != null) {
					is.close();
				}

				if (urlConn != null) {
					urlConn.disconnect();
				}

				return null;
			}
		} else
			resp = null;

		if (bufferedInputStream != null) {
			bufferedInputStream.close();
		}
		if (is != null) {
			is.close();
		}
		if (urlConn != null) {
			urlConn.disconnect();
		}
		return resp;

		// HashMap<String, Object> resp = new HashMap<String, Object>();
		//
		// HttpParams params = new BasicHttpParams();
		// // add the timeout
		// HttpConnectionParams.setConnectionTimeout(params, TIMEOUT);
		// HttpConnectionParams.setSoTimeout(params, TIMEOUT);
		//
		// try {
		// DefaultHttpClient httpClient = new DefaultHttpClient(params);
		// HttpGet request = new HttpGet(imageUrl);
		// HttpResponse response = httpClient.execute(request);
		//
		// if (requestSuccess(response)) {
		// request.abort();
		// response = null;
		// }
		//
		// if (response != null) {
		// try {
		// InputStream is = response.getEntity().getContent();
		// BufferedInputStream bufferedInputStream = new BufferedInputStream(
		// is);
		//
		// Bitmap bitmap = null;
		//
		// System.gc();
		// bitmap = BitmapFactory.decodeStream(bufferedInputStream);
		//
		// resp.put("reqsuccess", true);
		// resp.put("context", ctx);
		// resp.put("imageUrl", imageUrl);
		// resp.put("bitmap", bitmap);
		//
		// } catch (MalformedURLException e) {
		// MyLog.e(TAG, "fetchDrawable failed", e);
		// return null;
		// } catch (Exception e) {
		// MyLog.e(TAG, "fetchDrawable failed", e);
		// return null;
		// } catch (OutOfMemoryError e) {
		// MyLog.e(TAG, "fetchDrawable failed", e);
		// return null;
		// }
		// }
		//
		// } catch (Exception e) {
		// MyLog.e(TAG, e.toString());
		// }
		// return resp;
	}

	protected static HashMap<String, Object> getAppStateList(Context ctx,
			String applist) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyEightParams(ctx, applist);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_APPSTAT_LIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_APPSTAT_LIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_APPSTAT_LIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {
					JSONArray appArray = jsonResp.getJSONArray("list");

					ArrayList<App> appList = new ArrayList<App>();
					MyLog.d(TAG, "get app state list >>> app list size: "
							+ appArray.length());
					if (appArray != null) {
						int size = appArray.length();

						for (int i = 0; i < size; i++) {
							App a = new App();

							JSONObject jo = appArray.getJSONObject(i);

							a.setId(Integer.parseInt(jo.getString("appid")));

							a.setScore(Integer.parseInt(jo
									.getString("total_score")));
							a.setScoreCount(Integer.parseInt(jo
									.getString("total_num")));
							a.setCommentCount(Integer.parseInt(jo
									.getString("total_rate_num")));

							a.setLanguage(Integer.parseInt(jo
									.getString("is_english")));
							try {
								a.setDownloadCount(jo.getInt("download_cnt"));
							} catch (JSONException e) {
								a.setDownloadCount(0);
							}
							try {
								a.setInfoVersion(jo.getInt("infover"));
							} catch (JSONException e) {
								a.setInfoVersion(0);
							}
							appList.add(a);
						}
					}

					resp.put("list", appList);
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyEightParams(Context ctx, String applist)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();

		addBasicParams(params, 28, ctx);

		params.putString("applist", applist);

		return params;
	}

	private static Bundle getProtoElevenParams(Context ctx, String appid,
			String payid, String source) {
		Bundle params = new Bundle();

		addBasicParams(params, 11, ctx);

		params.putString("appid", appid);
		params.putString("payid", payid);
		params.putString("source", source);

		return params;
	}

	private static Bundle getProtoTwelveParams(Context ctx, String appid,
			String downloadId) {
		Bundle params = new Bundle();

		addBasicParams(params, 12, ctx);

		// time format
		Date date = new Date(System.currentTimeMillis());

		params.putString(
				"list",
				"appid=" + appid + ",download_id=" + downloadId
						+ ",download_time="
						+ DateFormat.format("yyyy-MM-dd hh:MM:ss", date));

		return params;
	}

	protected static HashMap<String, Object> authenticate(Context ctx,
			String appid) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyNineParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_AUTHENTICATE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_AUTHENTICATE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_AUTHENTICATE, paramsPair);

		if (requestSuccess(response)) {
			String result = response.toString();
			MyLog.d(TAG, "HTTP " + result);

			// use DES decode result
			result = SecurityUtil.getDESDecrypt(result);

			MyLog.d(TAG, "HTTP Decrypt" + result);

			JSONObject jsonResp = HTTPUtil.getJsonObject(result, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyNineParams(Context ctx, String appid)
			throws ClientProtocolException, IOException, JSONException {
		Bundle encryptParams = new Bundle();

		// generator DES key
		String DESkey = SecurityUtil.generatorDESRandomKey();

		// use RSA encrypt DES key
		String keyEncode = SecurityUtil.getMARKETRSAEncrypt(DESkey);

		String params = 29 + "|" + GeneralUtil.getUid(ctx) + "|"
				+ GeneralUtil.getSid(ctx) + "|" + GeneralUtil.getMid(ctx) + "|"
				+ appid;

		// use DES encrypt parameters
		String stringEncode = SecurityUtil.getDESEncrypt(params);
		MyLog.d(TAG, " stringEncode:" + stringEncode);

		addBasicParams(encryptParams, 29, ctx);
		encryptParams.putString("keyEncode", keyEncode);
		encryptParams.putString("stringEncode", stringEncode);

		return encryptParams;
	}

	protected static HashMap<String, Object> getLocalApplist(Context ctx,
			String applist) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyParams(ctx, applist);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_LOCAL_APPLIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_LOCAL_APPLIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_LOCAL_APPLIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {
					resp.put("list",
							getAppListByJSON(jsonResp.getJSONArray("list")));
				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoThirtyParams(Context ctx, String applist)
			throws ClientProtocolException, IOException, JSONException {
		Bundle params = new Bundle();

		addBasicParams(params, 30, ctx);

		params.putString("list", applist);

		return params;
	}

	public static HashMap<String, Object> uploadUserLogList(Context ctx,
			String userlogList) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoTwentyFiveParams(ctx, userlogList);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);

		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.HOST_USERLOG_CURRENT, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoTwentyFiveParams(Context ctx,
			String userlogList) {
		Bundle params = new Bundle();

		addBasicParams(params, 25, ctx);
		params.putString("action", "marketclient");
		params.putString("v", ConstantValues.CLIENT_VERSION_NUMBER + "");
		params.putString("fcode", userlogList);

		return params;
	}

	private static Bundle getProtoThirtyOneParams(Context ctx) {
		Bundle params = new Bundle();

		addBasicParams(params, 31, ctx);

		return params;
	}

	protected static HashMap<String, Object> getNewestCateData(Context ctx,
			String appid) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyTwoParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_NEWEST_CATE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_NEWEST_CATE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_NEWEST_CATE, paramsPair);

		if (requestSuccess(response)) {
			// JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			//
			// if (jsonResp != null) {
			// resp.put("proto", jsonResp.getInt("proto"));
			//
			// boolean reqStatus = jsonResp.getBoolean("reqsuccess");
			// resp.put("reqsuccess", reqStatus);
			//
			// MyLog.d(TAG, "get app list >>> status: " + reqStatus);
			// if (reqStatus) {
			// Category c = new Category();
			//
			// c.setAppCount(jsonResp.getInt("appcnt"));
			// c.setNewFreeAppList(jsonResp.getString("time_applist"));
			//
			// resp.put("cate", c);
			//
			// resp.put("list", getAppListByJSON(jsonResp
			// .getJSONArray("list")));
			//
			// } else {
			// storeErrorMsg(resp, jsonResp, ctx);
			// }
			// }
		}

		return resp;
	}

	private static Bundle getProtoThirtyTwoParams(Context ctx, String appid) {
		Bundle params = new Bundle();

		addBasicParams(params, 32, ctx);
		params.putString("appid", appid);

		return params;
	}

	protected static HashMap<String, Object> getOneCateApplist(Context ctx,
			String cateid) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyThreeParams(ctx, cateid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_ONE_CATE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_ONE_CATE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_ONE_CATE, paramsPair);

		if (requestSuccess(response)) {
			// JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			//
			// if (jsonResp != null) {
			// resp.put("proto", jsonResp.getInt("proto"));
			//
			// boolean reqStatus = jsonResp.getBoolean("reqsuccess");
			// resp.put("reqsuccess", reqStatus);
			//
			// MyLog.d(TAG, "get app list >>> status: " + reqStatus);
			// if (reqStatus) {
			// Category c = new Category();
			//
			// c.setSig(Uri.decode(jsonResp.getString("sig")));
			// c.setAppCount(jsonResp.getInt("appcnt"));
			//
			// c.setIsChart(jsonResp.getBoolean("is_chart"));
			//
			// c.setNewFreeAppList(jsonResp.getString("time_applist"));
			//
			// c.setNewAppName(Uri.decode(jsonResp
			// .getString("time_appname")));
			//
			// if (!c.getIsChart()) {
			// c.setHotFreeAppList(jsonResp.getString("down_applist"));
			// }
			//
			// // special for RECENT DOWNLOAD CATE
			// if (cateid.equals(ConstantValues.SUGGESTED_CATE_IDLIST[2])) {
			// GeneralUtil.saveDownloadTime(ctx, jsonResp
			// .getString("downtime_list"));
			// }
			//
			// resp.put("cate", c);
			//
			// } else {
			// storeErrorMsg(resp, jsonResp, ctx);
			// resp.put("cateid", cateid);
			// }
			// }
		}

		return resp;
	}

	private static Bundle getProtoThirtyThreeParams(Context ctx, String cateid) {
		Bundle params = new Bundle();

		addBasicParams(params, 33, ctx);
		params.putString("cateid", cateid);

		return params;
	}

	public static HashMap<String, Object> sendSuggestion(Context ctx,
			String suggestion, String email) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyFourParams(ctx, suggestion, email);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SENDSUGGESTION, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_SENDSUGGESTION");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_SENDSUGGESTION, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (!reqStatus) {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoThirtyFourParams(Context ctx,
			String suggestion, String email) {
		Bundle params = new Bundle();

		addBasicParams(params, 34, ctx);
		params.putString("content", suggestion);
		params.putString("email", email);

		return params;
	}

	public static HashMap<String, Object> getRelatedAppList(Context ctx,
			String appid) throws ClientProtocolException, IOException,
			JSONException {

		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyFiveParams(ctx, appid);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GETRELATEAPPLIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GETRELATEAPPLIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GETRELATEAPPLIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					String appidlist = jsonResp.getString("list");
					resp.put("list", appidlist);
				}

			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	private static Bundle getProtoThirtyFiveParams(Context ctx, String appid) {
		Bundle params = new Bundle();

		addBasicParams(params, 35, ctx);
		params.putString("appid", appid);

		return params;
	}

	public static HashMap<String, Object> getFavoriteChannel(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyEightParams(ctx);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_FAVORITE_CHANNEL, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_FAVORITE_CHANNEL");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_FAVORITE_CHANNEL, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {
					resp.put("list", jsonResp.getString("list"));
				}

			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	private static Bundle getProtoThirtyEightParams(Context ctx) {
		Bundle params = new Bundle();

		addBasicParams(params, 38, ctx);

		return params;
	}

	public static HashMap<String, Object> setFavoriteChannel(Context ctx,
			String channelList) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoThirtyNineParams(ctx, channelList);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_SET_FAVORITE_CHANNEL, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_SET_FAVORITE_CHANNEL");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_SET_FAVORITE_CHANNEL, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	private static Bundle getProtoThirtyNineParams(Context ctx,
			String channelList) {
		Bundle params = new Bundle();

		addBasicParams(params, 39, ctx);
		params.putString("channel_list", channelList);

		return params;
	}

	protected static HashMap<String, Object> getRecommmend(Context ctx,
			String recommendId) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyOneParams(ctx, recommendId);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_RECOMMEND, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_RECOMMEND");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_RECOMMEND, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {
					JSONArray recommendArray = jsonResp.getJSONArray("list");
					ArrayList<Recommend> recommendList = null;
					if (recommendArray != null) {
						recommendList = new ArrayList<Recommend>();
						int size = recommendArray.length();

						for (int i = 0; i < size; i++) {
							Recommend r = new Recommend();

							JSONObject jo = recommendArray.getJSONObject(i);

							r.setId(Integer.parseInt(jo.getString("id")));
							r.setName(Uri.decode(jo.getString("typename")));
							r.setTargetType(Integer.parseInt(jo
									.getString("type")));
							r.setTargetId(jo.getString("typeid"));
							r.setDesc(Uri.decode(jo.getString("desc")));
							r.setDate(Uri.decode(jo.getString("addtime")));
							r.setIconUrl(jo.getString("icon"));
							r.setImageUrl(jo.getString("image"));

							recommendList.add(r);

						}
					}

					resp.put("list", recommendList);
					resp.put("display", jsonResp.getString("display"));

				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoFortyOneParams(Context ctx, String recommendId) {
		Bundle params = new Bundle();

		addBasicParams(params, 41, ctx);
		params.putString("id", recommendId);
		return params;
	}

	protected static HashMap<String, Object> clientCheck(Context ctx)
			throws ClientProtocolException, IOException, JSONException {
		// HashMap<String, Object> resp = new HashMap<String, Object>();
		// send launch net_type to server
		ConnectivityManager CM = (ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo info = CM.getActiveNetworkInfo();

		Bundle params = getProtoFortyThreeParams(ctx);
		if (info != null) {
			params.putString(ConstantValues.NET_TYPE, info.getTypeName());
			params.putString(ConstantValues.NET_EXTRA, info.getExtraInfo());
		}
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response =
		// HTTPUtil.getHttpPostResponse(ctx,ConstantValues.URL_CLIENT_CHECK,
		// paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_CLIENT_CHECK");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_CLIENT_CHECK, paramsPair);

		if (requestSuccess(response)) {
			return null;
		}

		MyLog.e(TAG, "Net work failed!");
		return null;
	}

	private static Bundle getProtoFortyThreeParams(Context ctx) {
		Bundle params = new Bundle();

		addBasicParams(params, 43, ctx);

		return params;
	}

	private static ArrayList<App> getAppListByJSON(JSONArray appArray)
			throws JSONException {
		ArrayList<App> appList = new ArrayList<App>();
		MyLog.d(TAG, "get app list >>> app list size: " + appArray.length());
		if (appArray != null) {
			int size = appArray.length();

			for (int i = 0; i < size; i++) {
				App a = new App();

				JSONObject jo = appArray.getJSONObject(i);

				a.setId(Integer.parseInt(jo.getString("appid")));
				a.setIconUrl(Uri.decode(jo.getString("icon")));
				a.setName(Uri.decode(jo.getString("name")));
				// a.setSlogan(Uri.decode(jo.getString("short_desc")));
				// a.setDescription(Uri.decode(jo.getString("desc").replaceAll(
				// "\r", "")));
				a.setDescription(jo.getString("desc").replaceAll("\r", ""));// remove
				// Uri.decode(),solved
				// problem
				// about
				// %
				// Garbage
				a.setAuthorName(Uri.decode(jo.getString("author")));

				a.setScore(Integer.parseInt(jo.getString("total_score")));
				a.setScoreCount(Integer.parseInt(jo.getString("total_num")));
				a.setCommentCount(Integer.parseInt(jo
						.getString("total_rate_num")));

				a.setLanguage(Integer.parseInt(jo.getString("is_english")));

				try {
					a.setDownloadCount(jo.getInt("download_cnt"));
				} catch (JSONException e) {
					a.setDownloadCount(0);
				}
				try {
					a.setSize(jo.getInt("size"));
				} catch (JSONException e) {
					a.setSize(0);
				}
				String price = Uri.decode(jo.getString("price"));
				a.setPrice(price);

			try {
					a.setVersion(jo.getInt("version"));
				} catch (JSONException e) {
					a.setVersion(0);
				}
				a.setAppVersion(Uri.decode(jo.getString("appver")));
				try {
					a.setInfoVersion(jo.getInt("infover"));
				} catch (JSONException e) {
					a.setInfoVersion(0);
				}
				a.setScreenshotUrl(Uri.decode(jo.getString("screenshots")));

				a.setPackageName(jo.getString("pkg_name"));
				appList.add(a);
			}
		}

		return appList;
	}

	/**
	 * 
	 * @param ctx
	 * @param time
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	protected static HashMap<String, Object> getRecommmendByTime(Context ctx,
			String time) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyFourParams(ctx, time);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_RECOMMEND_BY_TIME, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_RECOMMEND_BY_TIME");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_RECOMMEND_BY_TIME, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {
					JSONArray recommendArray = jsonResp.getJSONArray("list");
					ArrayList<Recommend> recommendList = null;
					if (recommendArray != null) {
						recommendList = new ArrayList<Recommend>();
						int size = recommendArray.length();

						for (int i = 0; i < size; i++) {
							Recommend r = new Recommend();

							JSONObject jo = recommendArray.getJSONObject(i);

							r.setId(Integer.parseInt(jo.getString("id")));
							r.setName(Uri.decode(jo.getString("typename")));
							r.setTargetType(Integer.parseInt(jo
									.getString("type")));
							r.setTargetId(jo.getString("typeid"));
							r.setDesc(Uri.decode(jo.getString("desc")));
							r.setRecDesc(Uri.decode(jo.getString("rec_desc")));
							r.setDate(Uri.decode(jo.getString("addtime")));
							r.setIconUrl(jo.getString("icon"));
							r.setImageUrl(jo.getString("image"));

							recommendList.add(r);

						}
					}

					resp.put("list", recommendList);
					resp.put("display", jsonResp.getString("display"));
					resp.put("time", jsonResp.getString("time"));

				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param time
	 * @return
	 */
	private static Bundle getProtoFortyFourParams(Context ctx, String time) {
		Bundle params = new Bundle();

		addBasicParams(params, 44, ctx);
		params.putString("time", time);
		return params;
	}

	/**
	 * 
	 * @param ctx
	 * @param cateid
	 * @param type
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	protected static HashMap<String, Object> getOnecateApplistNew(Context ctx,
			String cateid, String type) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyFiveParams(ctx, cateid, type);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_ONECATE_APPLIST_NEW, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_ONECATE_APPLIST_NEW");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_ONECATE_APPLIST_NEW, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);
			if (jsonResp != null) {
				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);
				MyLog.d(TAG, "get onecate applist new>>> status: " + reqStatus);
				resp.put("proto", jsonResp.getInt("proto"));

				if (reqStatus) {
					Category cate = null;
					if (type == "4" || "4".equals(type)) {// type=4
						cate = new Category();
						cate.setAppList1(jsonResp.getString("week_applist"));
						cate.setAppList2(jsonResp.getString("month_applist"));
						cate.setAppList3(jsonResp.getString("all_applist"));
					} else {// type=1,2,3
						cate = new Category();
						cate.setAppList1(jsonResp.getString("time_applist"));
						if (type == "3" || "3".equals(type)) {
							cate.setAppList2(jsonResp.getString("down_applist"));
						}

						if (Integer.parseInt(cateid) == ConstantValues.DOWNLOAD_CATE_ID) {
							GeneralUtil.saveDownloadTime(ctx,
									jsonResp.getString("timelist"));
							GeneralUtil.saveNickName(ctx,
									jsonResp.getString("namelist"));
							GeneralUtil.saveTextViewUpdateTime(ctx,
									jsonResp.getString("updatetime"));
						}

					}
					resp.put("cate", cate);

				}
			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}
		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param cateid
	 * @param type
	 * @return
	 */
	private static Bundle getProtoFortyFiveParams(Context ctx, String cateid,
			String type) {
		Bundle params = new Bundle();

		addBasicParams(params, 45, ctx);
		params.putString("cateid", cateid);
		params.putString("type", type);
		return params;
	}

	/**
	 * 
	 * @param ctx
	 * @param updatetime
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	protected static HashMap<String, Object> getCateListNew(Context ctx,
			String updatetime) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortySixParams(ctx, updatetime);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_CATE_LIST_NEW, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_CATE_LIST_NEW");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_CATE_LIST_NEW, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);
				MyLog.d(TAG, "get cate list>>> status: " + reqStatus);

				if (reqStatus) {
					JSONArray cateArray = jsonResp.getJSONArray("list");
					ArrayList<Category> cateList = null;
					if (cateArray != null) {
						cateList = new ArrayList<Category>();
						int size = cateArray.length();

						for (int i = 0; i < size; i++) {
							Category cate = new Category();

							JSONObject jo = cateArray.getJSONObject(i);

							cate.setSig(Integer.parseInt(jo.getString("cateid")));
							cate.setType(Integer.parseInt(jo.getString("type")));
							cate.setParentId(Integer.parseInt(jo
									.getString("parentid")));
							cate.setName(Uri.decode(jo.getString("name")));
							cate.setDescription(Uri.decode(jo.getString("desc")));
							try {
								cate.setAppCount(jo.getInt("appcount"));
							} catch (JSONException e) {
								cate.setAppCount(0);
							}
							cate.setIconUrl(jo.getString("icon"));
							cate.setUpdateInterval(Long.parseLong(jo
									.getString("update_interval")));
							try {
								cate.setCateOrder(jo.getInt("order"));
							} catch (JSONException e) {
								cate.setCateOrder(0);
							}
							cateList.add(cate);

						}
					}

					resp.put("list", cateList);
					resp.put("showlist", jsonResp.getString("showlist"));
					resp.put("updatetime", jsonResp.getString("updatetime"));
				}

			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		} else {
			return null;
		}

		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param updatetime
	 * @return
	 */
	private static Bundle getProtoFortySixParams(Context ctx, String updatetime) {
		Bundle params = new Bundle();

		addBasicParams(params, 46, ctx);
		params.putString("updatetime", updatetime);
		return params;
	}

	public static HashMap<String, Object> getOneCateApplistPage(Context ctx,
			String cateid, String type, String orderType, String pageNo,
			String perpage) throws IOException, IOException, JSONException {

		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortySenvenParams(ctx, cateid, type, orderType,
				pageNo, perpage);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_ONECATE_APPLIST_PAGE, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_ONECATE_APPLIST_PAGE");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_ONECATE_APPLIST_PAGE, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				if (reqStatus) {

					String appidlist = jsonResp.getString("applist");
					resp.put("list", appidlist);

					if (Integer.parseInt(cateid) == ConstantValues.DOWNLOAD_CATE_ID) {
						GeneralUtil.saveDownloadTime(ctx,
								jsonResp.getString("timelist"));
						GeneralUtil.saveNickName(ctx,
								jsonResp.getString("namelist"));
						GeneralUtil.saveTextViewUpdateTime(ctx,
								jsonResp.getString("updatetime"));

					}

				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param cateid
	 * @param type
	 * @param orderType
	 * @param pageNo
	 * @param perpage
	 * @return
	 */
	private static Bundle getProtoFortySenvenParams(Context ctx, String cateid,
			String type, String orderType, String pageNo, String perpage) {
		Bundle params = new Bundle();

		addBasicParams(params, 47, ctx);
		params.putString("cateid", cateid);
		params.putString("type", type);
		params.putString("orderType", orderType);
		params.putString("pageNo", pageNo);
		params.putString("perpage", perpage);
		return params;
	}

	/**
	 * 
	 * @param ctx
	 * @param appid
	 * @param infoversion
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */

	public static HashMap<String, Object> getInfoVersion(Context ctx,
			String appid, String infoversion) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyEightParams(ctx, appid, infoversion);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_INFO_VERSION, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_INFO_VERSION");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_INFO_VERSION, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getString("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get app list >>> status: " + reqStatus);
				App app = new App();
				if (reqStatus) {
					if (!(infoversion == null)
							&& !(infoversion.equals(jsonResp
									.getString("infover")))) {
						app.setId(Integer.parseInt(jsonResp.getString("appid")));
						app.setIconUrl(Uri.decode(jsonResp.getString("icon")));
						app.setName(Uri.decode(jsonResp.getString("name")));
						// app.setSlogan(Uri.decode(jsonResp.getString("short_desc")));
						app.setDescription(Uri.decode(jsonResp
								.getString("desc").replaceAll("\r", "")));
						app.setAuthorName(Uri.decode(jsonResp
								.getString("author")));
						try {
							app.setDownloadCount(jsonResp.getInt("download_cnt"));
						} catch (JSONException e) {
							app.setDownloadCount(0);
						}
						app.setPrice(Uri.decode(jsonResp.getString("price")));
						try {
							app.setVersion(Integer.parseInt(jsonResp
									.getString("version")));
						} catch (JSONException e) {
							app.setVersion(0);
						}

						app.setAppVersion(Uri.decode(jsonResp
								.getString("appver")));
						try {
							app.setInfoVersion(jsonResp.getInt("infover"));
						} catch (JSONException e) {
							app.setInfoVersion(0);
						}
						try {
							app.setSize(jsonResp.getInt("size"));
						} catch (JSONException e) {
							app.setSize(0);
						}
						app.setPackageName(jsonResp.getString("pkg_name"));

						try {
							app.setLanguage(jsonResp.getInt("is_english"));
						} catch (JSONException e) {
							app.setLanguage(0);
						}
						try {
							app.setScore(jsonResp.getInt("total_score"));
						} catch (JSONException e) {
							app.setScore(0);
						}
						try {
							app.setScoreCount(jsonResp.getInt("total_num"));
						} catch (JSONException e) {
							app.setScoreCount(0);
						}
						try {
							app.setCommentCount(jsonResp.getInt("total_rate_num"));
						} catch (JSONException e) {
							app.setCommentCount(0);
						}
//						app.setLanguage(Integer.parseInt(jsonResp
//								.getString("is_english")));
//						app.setScore(Integer.parseInt(jsonResp
//								.getString("total_score")));
//						app.setScoreCount(Integer.parseInt(jsonResp
//								.getString("total_num")));
//
//						app.setCommentCount(Integer.parseInt(jsonResp
//								.getString("total_rate_num")));
						app.setScreenshotUrl(jsonResp.getString("screenshots"));

						resp.put("app", app);
					} else {
						app.setId(Integer.parseInt(jsonResp.getString("appid")));
						try {
							app.setDownloadCount(jsonResp.getInt("download_cnt"));
						} catch (JSONException e) {
							app.setDownloadCount(0);
						}						
						try {
							app.setInfoVersion(jsonResp.getInt("infover"));
						} catch (JSONException e) {
							app.setInfoVersion(0);
						}
						try {
							app.setScore(jsonResp.getInt("total_score"));
						} catch (JSONException e) {
							app.setScore(0);
						}
						try {
							app.setScoreCount(jsonResp.getInt("total_num"));
						} catch (JSONException e) {
							app.setScoreCount(0);
						}
						try {
							app.setCommentCount(jsonResp.getInt("total_rate_num"));
						} catch (JSONException e) {
							app.setCommentCount(0);
						}

						resp.put("app", app);
					}
				}
			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param appid
	 * @param infoversion
	 * @return
	 */
	private static Bundle getProtoFortyEightParams(Context ctx, String appid,
			String infoversion) {
		Bundle params = new Bundle();

		addBasicParams(params, 48, ctx);
		params.putString("appid", appid);
		params.putString("infoversion", infoversion);
		return params;
	}

	/**
	 * 
	 * @param ctx
	 * @param count
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	protected static HashMap<String, Object> getHotwordsList(Context ctx,
			String count) throws ClientProtocolException, IOException,
			JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFortyNineParams(ctx, count);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_HOTWORDS_LIST_NEW, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_HOTWORDS_LIST_NEW");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_HOTWORDS_LIST_NEW, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);
				MyLog.d(TAG, "get hotwords list >>> status: " + reqStatus);
				if (reqStatus) {
					resp.put("list", jsonResp.getString("list"));
				}

			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	/**
	 * 
	 * @param ctx
	 * @param count
	 * @return
	 */
	private static Bundle getProtoFortyNineParams(Context ctx, String count) {
		Bundle params = new Bundle();

		addBasicParams(params, 49, ctx);
		params.putString("count", count);
		return params;
	}

	protected static HashMap<String, Object> getEventTimes(Context ctx,
			String updatetime) throws ClientProtocolException, IOException,
			JSONException {
		MyLog.d(TAG, "send updatetime>>>>>updatetime:" + updatetime);
		MyLog.d(TAG, "getEventTimes>>>loding enents times from server!");
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiftyParams(ctx, updatetime);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_DOWNLOADS_TIME, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_DOWNLOADS_TIME");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_DOWNLOADS_TIME, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get getEventTimes >>> status: " + reqStatus);
				if (reqStatus) {
					resp.put("update_interval", Integer.valueOf(jsonResp
							.getString("update_interval")));
					resp.put("count",
							Integer.valueOf(jsonResp.getString("count")));
					MyLog.d(TAG,
							"get count >>> count: "
									+ jsonResp.getString("count"));
					MyLog.d(TAG, "get time_interval >>> time_interval: "
							+ jsonResp.getString("update_interval"));

				}
			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	private static Bundle getProtoFiftyParams(Context ctx, String updatetime) {
		Bundle params = new Bundle();

		addBasicParams(params, 50, ctx);
		params.putString("updatetime", updatetime);
		return params;
	}

	protected static HashMap<String, Object> getBackupAndRecoveryList(
			Context ctx, String type, String applist)
			throws ClientProtocolException, IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiftyOneParams(ctx, type, applist);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_GET_BACKUP_AND_RECOVERY_APPLIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_GET_BACKUP_AND_RECOVERY_APPLIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_GET_BACKUP_AND_RECOVERY_APPLIST, paramsPair);

		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				MyLog.d(TAG, "get backup app list >>> status: " + reqStatus);
				if (reqStatus) {

					resp.put("applist", jsonResp.getString("applist"));
					MyLog.d(TAG,
							"get backup app list="
									+ jsonResp.getString("applist"));

				}
			} else {
				storeErrorMsg(resp, jsonResp, ctx);
			}
		}

		return resp;
	}

	private static Bundle getProtoFiftyOneParams(Context ctx, String type,
			String applist) {
		Bundle params = new Bundle();

		addBasicParams(params, 51, ctx);
		params.putString("type", type);
		params.putString("applist", applist);
		return params;
	}

	public static HashMap<String, Object> downloadAppList(Context ctx,
			String appidList, String source) throws ClientProtocolException,
			IOException, JSONException {
		HashMap<String, Object> resp = new HashMap<String, Object>();

		Bundle params = getProtoFiftyTwoParams(ctx, appidList, source);
		List<NameValuePair> paramsPair = HTTPUtil.getPostRequestParams(params);
		// String response = null;
		// try {
		// response = HTTPUtil.getHttpPostResponse(ctx,
		// ConstantValues.URL_DOWNLOAD_LIST, paramsPair);
		// } catch (Exception e) {
		MyLog.ee("URL_DOWNLOAD_LIST");
		// }
		String response = HTTPUtil.getHttpPostResponse(ctx,
				ConstantValues.URL_DOWNLOAD_LIST, paramsPair);
		if (requestSuccess(response)) {
			JSONObject jsonResp = HTTPUtil.getJsonObject(response, false);

			if (jsonResp != null) {
				resp.put("proto", jsonResp.getInt("proto"));

				boolean reqStatus = jsonResp.getBoolean("reqsuccess");
				resp.put("reqsuccess", reqStatus);

				if (reqStatus) {

					JSONArray downArray = jsonResp.getJSONArray("list");
					String appIDList = "";
					String urlList = "";
					String downloadIDList = "";

					if (downArray != null) {

						int size = downArray.length();

						for (int i = 0; i < size; i++) {
							JSONObject jo = downArray.getJSONObject(i);

							if (appIDList.equals(""))
								appIDList += jo.getString("appid");
							else
								appIDList += "," + jo.getString("appid");

							if (urlList.equals(""))
								urlList += jo.getString("location");
							else
								urlList += "," + jo.getString("location");

							if (downloadIDList.equals(""))
								downloadIDList += jo.getString("download_id");
							else
								downloadIDList += ","
										+ jo.getString("download_id");

						}
					}

					resp.put("appid_list", appIDList);
					resp.put("url_list", urlList);
					resp.put("download_id_list", downloadIDList);

				} else {
					storeErrorMsg(resp, jsonResp, ctx);
				}
			}
		}

		return resp;
	}

	private static Bundle getProtoFiftyTwoParams(Context ctx, String appidList,
			String source) {
		Bundle params = new Bundle();

		addBasicParams(params, 52, ctx);

		params.putString("appid_list", appidList);
		params.putString("source", source);

		return params;
	}

}
