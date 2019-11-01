package com.market.hjapp.tyxl;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;


import com.market.hjapp.R;
import com.market.hjapp.tyxl.AddressAdapter.ViewHolder;
import com.market.hjapp.tyxl.object.Address;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;
public class UpdateAddress extends Activity {
	private Button Add_address, Send;
	private LinearLayout linearLayout;
	private FrameLayout framelayout;

	public static UpdateAddress address;
	public static String userAddress;
	public int sign;
	MD5 md5;

	// 省
	// 省份选择框
	private Spinner Spinner_Province;
	private ArrayAdapter<String> Adapter_Province;
	// 省份选择框中数据索引
	private int Item_Province;
	// 省份对应id
	private int[] Id_Province = { -1, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
			13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
			30, 31, 32, 33, 34 };
	// 省份名
	private String[] Str_Province = { "所在省份", "北京", "天津", "上海", "重庆", "河北",
			"山西", "内蒙古", "辽宁", "吉林", "黑龙江", "江苏", "浙江", "安徽", "福建", "江西", "山东",
			"河南", "湖北", "湖南", "广东", "广西", "海南", "四川", "贵州", "云南", "西藏", "陕西",
			"甘肃", "青海", "宁夏", "新疆", "台湾", "香港", "澳门" };

	// 市
	// 城市选择框
	private Spinner Spinner_City;
	private ArrayAdapter<String> Adapter_City;
	// 城市选择框中数据索引
	private int Item_City;
	// 城市所对应id
	private int[][] Id_City = {
			{ -1 },
			{ -1, 1, 2 },
			{ -1, 3, 4 },
			{ -1, 75, 76 },
			{ -1, 238, 239, 240 },
			{ -1, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 },
			{ -1, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26 },
			{ -1, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38 },
			{ -1, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52 },
			{ -1, 53, 54, 55, 56, 57, 58, 59, 60, 61 },
			{ -1, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74 },
			{ -1, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89 },
			{ -1, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100 },
			{ -1, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112,
					113, 114, 115, 116, 117 },
			{ -1, 118, 119, 120, 121, 122, 123, 124, 125, 126 },
			{ -1, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137 },
			{ -1, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149,
					150, 151, 152, 153, 154 },
			{ -1, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166,
					167, 168, 169, 170, 171 },
			{ -1, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183,
					184, 185 },
			{ -1, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197,
					198, 199 },
			{ -1, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211,
					212, 213, 214, 215, 216, 217, 218, 219, 220 },
			{ -1, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232,
					233, 234 },
			{ -1, 235, 236, 237 },
			{ -1, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252,
					253, 254, 255, 256, 257, 258, 259, 260, 261 },
			{ -1, 262, 263, 264, 265, 266, 267, 268, 269, 270 },
			{ -1, 271, 272, 273, 274, 275, 276, 277, 278, 279, 280, 281, 282,
					283, 284, 285, 286 },
			{ -1, 287, 288, 289, 290, 291, 292, 293 },
			{ -1, 294, 295, 296, 297, 298, 299, 300, 301, 302, 303 },
			{ -1, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315,
					316, 317 },
			{ -1, 318, 319, 320, 321, 322, 323, 324, 325 },
			{ -1, 326, 327, 328, 329 },
			{ -1, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341,
					342, 343, 344, 345 }, { -1 }, { -1 }, { -1 }, };
	// 城市名
	private String[][] Str_City = {
			{ "所在城市" },
			{ "所在城市", "北京市辖", "北京县辖" },
			{ "所在城市", "天津市辖", "天津县辖" },
			{ "所在城市", "上海市辖", "上海县辖" },
			{ "所在城市", "重庆市辖", "重庆县辖", "重庆县级" },
			{ "所在城市", "石家庄", "唐山", "秦皇岛", "邯郸", "邢台", "保定", "张家口", "承德", "沧州",
					"廊坊", "衡水" },
			{ "所在城市", "太原", "大同", "阳泉", "长治", "晋城", "朔州", "晋中", "运城", "忻州",
					"临汾", "吕梁地区" },
			{ "所在城市", "呼和浩特", "包头", "乌海", "赤峰", "通辽", "呼伦贝尔盟", "兴安盟", "锡林郭勒盟",
					"乌兰察布盟", "伊克昭盟", "巴彦淖尔盟", "阿拉善盟" },
			{ "所在城市", "沈阳", "大连", "鞍山", "抚顺", "本溪", "丹东", "锦州", "营口", "阜新",
					"辽阳", "盘锦", "铁岭", "朝阳", "葫芦岛" },
			{ "所在城市", "长春", "吉林", "松原", "白城", "延边朝鲜族自治州", "四平", "辽源", "通化",
					"白山" },
			{ "所在城市", "哈尔滨", "齐齐哈尔", "鸡西", "鹤岗", "双鸭山", "大庆", "伊春", "佳木斯",
					"七台河", "牡丹江", "黑河", "绥化", "大兴安岭地区" },
			{ "所在城市", "南京", "无锡", "徐州", "常州", "苏州", "南通", "连云港", "淮安", "盐城",
					"扬州", "镇江", "泰州", "宿迁" },
			{ "所在城市", "杭州", "宁波", "温州", "嘉兴", "湖州", "绍兴", "金华", "衢州", "舟山",
					"台州", "丽水" },
			{ "所在城市", "合肥", "芜湖", "蚌埠", "淮南", "马鞍山", "淮北", "铜陵", "安庆", "黄山",
					"滁州", "阜阳", "宿州", "巢湖", "六安", "亳州", "池州", "宣城" },
			{ "所在城市", "福州", "厦门", "莆田", "三明", "泉州", "漳州", "南平", "龙岩", "宁德" },
			{ "所在城市", "南昌", "景德镇", "萍乡", "九江", "新余", "鹰潭", "赣州", "吉安", "宜春",
					"抚州", "上饶" },
			{ "所在城市", "济南", "青岛", "淄博", "枣庄", "东营", "烟台", "潍坊", "济宁", "泰安",
					"威海", "日照", "莱芜", "临沂", "德州", "聊城", "滨州", "菏泽" },
			{ "所在城市", "郑州", "开封", "洛阳", "平顶山", "安阳", "鹤壁", "新乡", "焦作", "濮阳",
					"许昌", "漯河", "三门峡", "南阳", "商丘", "信阳", "周口", "驻马店" },
			{ "所在城市", "武汉", "黄石", "十堰", "宜昌", "襄樊", "鄂州", "荆门", "孝感", "荆州",
					"黄冈", "咸宁", "随州", "恩施土家族苗族自治州", "省直辖行政单位" },
			{ "所在城市", "长沙", "株洲", "湘潭", "衡阳", "邵阳", "岳阳", "常德", "张家界", "益阳",
					"郴州", "永州", "怀化", "娄底", "湘西土家族苗族自治州" },
			{ "所在城市", "广州", "韶关", "深圳", "珠海", "汕头", "佛山", "江门", "湛江", "茂名",
					"肇庆", "惠州", "梅州", "汕尾", "河源", "阳江", "清远", "东莞", "中山", "潮州",
					"揭阳", "云浮" },
			{ "所在城市", "南宁", "柳州", "桂林", "梧州", "北海", "防城港", "钦州", "贵港", "玉林",
					"南宁地区", "柳州地区", "贺州地区", "百色地区", "河池地区" },
			{ "所在城市", "海南", "海口", "三亚" },
			{ "所在城市", "成都", "自贡", "攀枝花", "泸州", "德阳", "绵阳", "广元", "遂宁", "内江",
					"乐山", "南充", "眉山", "宜宾", "广安", "达州", "雅安", "巴中", "资阳",
					"阿坝藏族羌族自治州", "甘孜藏族自治州", "凉山彝族自治州" },
			{ "所在城市", "贵阳", "六盘水", "遵义", "安顺", "铜仁地区", "黔西南布依族苗族自治", "毕节地区",
					"黔东南苗族侗族自治州", "黔南布依族苗族自治州" },
			{ "所在城市", "昆明", "曲靖", "玉溪", "保山", "昭通地区", "楚雄彝族自治州", "红河哈尼族彝族自治州",
					"文山壮族苗族自治州", "思茅地区", "西双版纳傣族自治州", "大理白族自治州", "德宏傣族景颇族自治州",
					"丽江地区", "怒江傈僳族自治州", "迪庆藏族自治州", "临沧地区" },
			{ "所在城市", "拉萨", "昌都地区", "山南地区", "日喀则地区", "那曲地区", "阿里地区", "林芝地区" },
			{ "所在城市", "西安", "铜川", "宝鸡", "咸阳", "渭南", "延安", "汉中", "榆林", "安康",
					"商洛地区" },
			{ "所在城市", "兰州", "嘉峪关", "金昌", "白银", "天水", "酒泉地区", "张掖地区", "武威地区",
					"定西地区", "陇南地区", "平凉地区", "庆阳地区", "临夏回族自治州", "甘南藏族自治州" },
			{ "所在城市", "西宁", "海东地区", "海北藏族自治州", "黄南藏族自治州", "海南藏族自治州", "果洛藏族自治州",
					"玉树藏族自治州", "海西蒙古族藏族自治州" },
			{ "所在城市", "银川", "石嘴山", "吴忠", "固原地区" },
			{ "所在城市", "乌鲁木齐", "克拉玛依", "吐鲁番地区", "哈密地区", "昌吉回族自治州", "博尔塔拉蒙古自治州",
					"巴音郭楞蒙古自治州", "阿克苏地区", "克孜勒苏柯尔克孜自治", "喀什地区", "和田地区",
					"伊犁哈萨克自治州", "伊犁地区", "塔城地区", "阿勒泰地区", "省直辖行政单位" },
			{ "所在城市" }, { "所在城市" }, { "所在城市" }, };

	// 地址相关数据输入框
	EditText txt_receiver, txt_street, txt_postcode, txt_tel;
	String receiver, street, postcode, tel;
	TextView id;
	// 省份id、城市id
	int provinceId, cityId;

	// 所选地址Id
	public static String useraddrId;
	// 修改按钮
	public Bitmap ModifyMap;
	// 删除按键
	public Bitmap DeleteMap;
	// 已存地址数据
	public static ArrayList<Address> address_vector = new ArrayList<Address>();

	ListView listView;
	protected Cursor mCursor = null;
	protected AddressAdapter ca;
	public static ArrayList<AddressInfo> contactList = new ArrayList<AddressInfo>();
	// 已存数据id
	private static String AddressId = "";
	// 奖项id
	private static String logId = "";
	// 奖项类型 0兑奖、1抽奖
	private static String type = "";
	// 上传数据类型 0 上传新数据、1 上传兑奖、2 上传抽奖 、3 修改依存数据
	private static int SendType = -1;
	LinearLayout bottom;
	// 标记是否在编辑地址 0：没有在编辑 1：在编辑
	private int back = 0;
	String[] selected;
	String invite_selectId = null;
	int size = 0, num = 0;
	int flag;
	private static final int DIALOG_WBE = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.update_address);

		linearLayout = (LinearLayout) findViewById(R.id.linearLayout);
		framelayout = (FrameLayout) findViewById(R.id.framelayout);

		Add_address = (Button) findViewById(R.id.add_address);
		Send = (Button) findViewById(R.id.send);
		Add_address.setOnClickListener(new btnClickListener());
		Send.setOnClickListener(new btnClickListener());

		address = this;
		md5 = new MD5();

		// getWindow().setSoftInputMode(
		// WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);
		//
		ModifyMap = BitmapFactory.decodeResource(address.getResources(),
				R.drawable.modify0);
		DeleteMap = BitmapFactory.decodeResource(address.getResources(),
				R.drawable.delete0);

		id = (TextView) findViewById(R.id.id);
		txt_receiver = (EditText) findViewById(R.id.txt_username);
		txt_street = (EditText) findViewById(R.id.txt_street);
		txt_postcode = (EditText) findViewById(R.id.txt_zipcode);
		txt_tel = (EditText) findViewById(R.id.txt_pone);

		txt_receiver.setHint(getResources().getString(R.string.text1));
		txt_street.setHint(getResources().getString(R.string.text2));
		txt_postcode.setHint(getResources().getString(R.string.text3));
		txt_tel.setHint(getResources().getString(R.string.text4));
		listView = (ListView) findViewById(android.R.id.list);
		listView.setChoiceMode(ListView.CHOICE_MODE_SINGLE);

		// 获取已存地址数据
		new GetAddressListTask().execute("");

		// 列表点击事件监听
		listView.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				ViewHolder item = (ViewHolder) view.getTag(); // 在每次获取点击的item时将对于的checkbox状态改变，同时修改map的值。
				item.check.toggle();
				ca.isSelected.put(position, item.check.isChecked());
				if (item.check.isChecked()) {
					selected[position] = item.Id;
					AddressId = contactList.get(position).AddressId;
					for (int i = 0; i < size; i++) {
						if (i != position) {
							ca.isSelected.put(i, false);
						}
					}
					// 区别兑奖和抽奖
					if (type.equals("1")) {
						setSendType(1);
					} else {
						setSendType(2);
					}
				} else {
					selected[position] = "";
					AddressId = "";
					setSendType(-1);
					invite_selectId = null;
				}
				flag = position;

			}
		});

		// 省
		Spinner_Province = (Spinner) findViewById(R.id.Spinner_Province);
		// 第二步：为下拉列表定义一个适配器
		Adapter_Province = new ArrayAdapter<String>(this,
				android.R.layout.simple_spinner_item, Str_Province);
		// 第三步：为适配器设置下拉列表下拉时的菜单样式
		Adapter_Province
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		// 第四步：将适配器添加到下拉列表上
		Spinner_Province.setAdapter(Adapter_Province);
		// 第五步：为下拉列表设置各种事件的响应，这个事响应菜单被选中
		Spinner_Province.setOnItemSelectedListener(ProvinceSelectListener);
		// Spinner_Province.setPrompt("aaaaa");

		// 市
		Spinner_City = (Spinner) findViewById(R.id.Spinner_City);
		Adapter_City = new ArrayAdapter<String>(this,
				android.R.layout.simple_spinner_item, Str_City[Item_Province]);
		Adapter_City
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		Spinner_City.setAdapter(Adapter_City);
		Spinner_City.setOnItemSelectedListener(CitySelectListener);
		// Spinner_City.setSelection(0, true);
		bottom = (LinearLayout) findViewById(R.id.bot);
	}

	/**
	 * 省份选择框
	 */
	private OnItemSelectedListener ProvinceSelectListener = new OnItemSelectedListener() {
		public void onItemSelected(AdapterView parent, View v, int position,
				long id) {
			// Item_Province（int）标记选中的省份对应的Position
			Item_Province = Spinner_Province.getSelectedItemPosition();

			Adapter_City = new ArrayAdapter<String>(address,
					android.R.layout.simple_spinner_item,
					Str_City[Item_Province]);
			Spinner_City.setAdapter(Adapter_City);
		}

		public void onNothingSelected(AdapterView arg0) {
		}
	};
	/**
	 * 城市选择框
	 */
	private OnItemSelectedListener CitySelectListener = new OnItemSelectedListener() {
		public void onItemSelected(AdapterView parent, View v, int position,
				long id) {
			Item_City = Spinner_City.getSelectedItemPosition();
			// Spinner_City.setSelection(1);//, true);
		}

		public void onNothingSelected(AdapterView arg0) {

		}

	};

	private void invite_friend() {
		int j = 0;
		for (int i = 0; i < size; i++) {
			if (selected[i] != "") {
				invite_selectId = selected[i];
			}
		}
	}

	class btnClickListener implements OnClickListener {

		@Override
		public void onClick(View v) {
			// TODO Auto-generated method stub
			switch (v.getId()) {
			case R.id.add_address:
				back = 1;
				setSendType(0);
				Spinner_Province.setSelection(0);
				txt_receiver.setText("");
				txt_street.setText("");
				txt_postcode.setText("");
				txt_tel.setText("");
				txt_receiver.setHint(getResources().getString(R.string.text1));
				txt_street.setHint(getResources().getString(R.string.text2));
				txt_postcode.setHint(getResources().getString(R.string.text3));
				txt_tel.setHint(getResources().getString(R.string.text4));
				linearLayout.setVisibility(8);
				Add_address.setVisibility(8);
				framelayout.setVisibility(0);
				break;
			case R.id.send:
				// 获取输入框编辑数据
				receiver = String.valueOf(txt_receiver.getText()).trim();
				street = String.valueOf(txt_street.getText()).trim();
				postcode = String.valueOf(txt_postcode.getText()).trim();
				tel = String.valueOf(txt_tel.getText()).trim();
				provinceId = Id_Province[Item_Province];
				cityId = Id_City[Item_Province][Item_City];

				if (SendType == -1) {
					invite_friend();
					if (invite_selectId == null) {

					} else {

					}
				}
				// 上传地址数据
				switch (SendType) {
				case -1:
					AlertDialog.Builder alertDialog = new AlertDialog.Builder(
							address);
					alertDialog.setTitle(getResources().getString(
							R.string.alert));
					alertDialog.setMessage(getResources().getString(
							R.string.alert_message45));
					alertDialog.setPositiveButton(
							getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									UpdateAddress.this.finish();
									return;
								}
							});
					alertDialog.create(); // 创建对话框
					alertDialog.show(); // 显示对话框
					break;
				case 0:
					// 新增地址
					back = 0;
					int sign = Judge(receiver, street, postcode, tel,
							provinceId, cityId);
					// JudgeDialog(UpdateAddress.this, sign);
					if (sign == 0) {
						new GetUploadAddrTask().execute("");
						// ca.notifyDataSetChanged();
						linearLayout.setVisibility(0);
						Add_address.setVisibility(0);
						framelayout.setVisibility(8);
					}
					break;
				case 1:
					// 兑奖地址
					new GetExchangeAddrTask().execute("");
					setSendType(-1);
					break;
				case 2:
					// 抽奖地址
					new GetLotteryAddrTask().execute("");
					setSendType(-1);
					break;
				case 3:
					// 修改已有地址
					back = 0;
					int sign1 = Judge(receiver, street, postcode, tel,
							provinceId, cityId);
					if (sign1 == 0) {
						new GetModifyAddrTask().execute("");
						linearLayout.setVisibility(0);
						Add_address.setVisibility(0);
						framelayout.setVisibility(8);
					}
					setSendType(-1);
					break;
				}
			}
		}
	}

	protected static int addressItem;

	// 修改数据
	public void gotoModify() {
		back = 1;
		linearLayout.setVisibility(8);
		Add_address.setVisibility(8);
		framelayout.setVisibility(0);
		for (int i = 0; i < address_vector.size(); i++) {
			if (address_vector.get(i).Id
					.equals(contactList.get(addressItem).AddressId)) {
				int flag = 0;// ,max=Id_City[0].length;
				for (int j = 0; j < Id_Province.length; j++) {
					if (Integer.parseInt(address_vector.get(i).provinceId) == Id_Province[j]) {
						Spinner_Province.setSelection(j);
						Item_Province = j;
						flag = j;
						break;
					}
				}

				for (int n = 0; n < Id_City[flag].length; n++) {
					if (Integer.parseInt(address_vector.get(i).cityId) == Id_City[flag][n]) {
						Adapter_City.notifyDataSetChanged();
						Spinner_City.setSelection(n);// , true);
						break;
					}
				}

				txt_receiver.setText(address_vector.get(i).receiver);
				txt_street.setText(address_vector.get(i).address);
				txt_postcode.setText(address_vector.get(i).postcode);
				txt_tel.setText(address_vector.get(i).tel);
			}
			// break;
		}

		setSendType(3);
	}

	// 删除
	public void gotoDelete() {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(address);
		alertDialog.setTitle(getResources().getString(R.string.alert_title6));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message46));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						new GetDeleteAddrTask().execute("");
					}
				});
		alertDialog.setNegativeButton(
				getResources().getString(R.string.cancel),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	/**
	 * 获取地址留档列表
	 * 
	 * @author user
	 * 
	 */
	private class GetAddressListTask extends AsyncTask<String, String, String> {

		String Province, City;

		public String doInBackground(String... params) {

			try {
				String addressList_url = HttpUrl.httpStr
						+ "user/useraddrs.action?userid=" + AdPlatform.userId
						+ "&password=" + md5.getMD5ofStr(AdPlatform.password);

				String addressList_out = HttpUrl.setGetUrl(addressList_url);

				JSONObject addressList_jsonObject = new JSONObject(
						addressList_out);
				JSONArray addressList_jsonArray = addressList_jsonObject
						.getJSONArray("useraddrs");
				for (int i = 0; i < addressList_jsonArray.length(); i++) {
					JSONObject addressList_jsonObject_0 = (JSONObject) addressList_jsonArray
							.opt(i);
					Address address = new Address();
					address.address = addressList_jsonObject_0
							.getString("address");
					address.cityId = addressList_jsonObject_0
							.getString("cityid");
					address.Id = addressList_jsonObject_0.getString("id");
					address.postcode = addressList_jsonObject_0
							.getString("postcode");
					address.provinceId = addressList_jsonObject_0
							.getString("provinceid");
					address.receiver = addressList_jsonObject_0
							.getString("receiver");
					address.tel = addressList_jsonObject_0.getString("tel");
					address.userId = addressList_jsonObject_0
							.getString("userid");
					if (!IsInAddressList(address_vector, address.Id)) {
						address_vector.add(address);
					}
				}
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetAddressListTask", "" + e);
			}

			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (address_vector.size() == 0) {
				} else {
					// 填充列表
					for (int i = 0; i < address_vector.size(); i++) {
						AddressInfo cci = new AddressInfo();
						cci.AddressId = address_vector.get(i).Id;
						for (int j = 0; j < Id_Province.length; j++) {
							if (address_vector.get(i).provinceId.equals(String
									.valueOf(Id_Province[j]))) {
								Province = Str_Province[j];
								for (int k = 0; k < Id_City[j].length; k++) {
									if (address_vector.get(i).cityId
											.equals(String
													.valueOf(Id_City[j][k]))) {
										City = Str_City[j][k];
									}
								}
							}
						}

						cci.UserAddress = Province + City
								+ address_vector.get(i).address;
						cci.Receiver = "收件人：" + address_vector.get(i).receiver;
						cci.Tel = "电话：" + address_vector.get(i).tel;
						if (contactList == null) {
							cci.IsChecked = true;
						} else {
							cci.IsChecked = false;
						}
						cci.ModifyMap = ModifyMap;
						cci.DeleteMap = DeleteMap;
						if (!IsInAddressInfo(contactList, cci.AddressId)) {
							contactList.add(cci);
						}
					}
					size = contactList.size();
					// invite_selectId = new String[size];
					selected = new String[size];
					ca = new AddressAdapter(UpdateAddress.this, contactList);
					listView.setAdapter(ca);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 上传地址 抽奖
	 * 
	 * @author user
	 * 
	 */
	private class GetLotteryAddrTask extends AsyncTask<String, String, String> {
		// 上传地址
		String str_lotteryaddr_result;

		public String doInBackground(String... params) {
			try {
				// 设置邮寄地址(post)==========
				String lotteryaddr_url = HttpUrl.httpStr
						+ "active/lotteryaddr.action?userid="
						+ AdPlatform.userId
						+ "&password="
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "&lotterylogid="
						+ logId
						+ "&useraddrid="
						+ AddressId
						+ "&md="
						+ md5.getMD5ofStr(AdPlatform.userId
								+ md5.getMD5ofStr(AdPlatform.password) + logId
								+ AddressId);
				String lotteryaddr_out = HttpUrl.setGetUrl(lotteryaddr_url);

				// result(上传地址结果)1成功 0失败
				JSONObject lotteryaddr_jsonObject = new JSONObject(
						lotteryaddr_out);
				str_lotteryaddr_result = lotteryaddr_jsonObject
						.getString("result");
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetLotteryAddrTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_lotteryaddr_result.equals("1")) {
					alertSendDialog(address, 0);
				} else {
					alertSendDialog(address, 1);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 修改
	 * 
	 * @author user
	 * 
	 */
	private class GetModifyAddrTask extends AsyncTask<String, String, String> {
		// 上传地址
		String str_modify_result;

		public String doInBackground(String... params) {
			try {
				// 设置邮寄地址(post)==========
				String modify_url = HttpUrl.httpStr + "user/updateaddr.action";
				String modify_in = "{\"logid\":" + logId + ",\"type\":" + type
						+ ",\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"provinceid\":" + provinceId + ",\"cityid\":"
						+ cityId + ",\"address\":\"" + street
						+ "\",\"receiver\":\"" + receiver + "\",\"tel\":\""
						+ tel + "\",\"postcode\":\"" + postcode
						+ "\",\"id\":\""
						+ contactList.get(addressItem).AddressId + "\"}";

				String modify_out = HttpUrl.setPostUrl(modify_url, modify_in);

				// result(上传地址结果)1成功 0失败
				JSONObject modify_jsonObject = new JSONObject(modify_out);
				str_modify_result = modify_jsonObject.getString("result");
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetModifyAddrTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_modify_result.equals("1")) {
					alertSendDialog(address, 2);
				} else {
					alertSendDialog(address, 3);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 删除
	 * 
	 * @author user
	 * 
	 */
	private class GetDeleteAddrTask extends AsyncTask<String, String, String> {
		// 上传地址
		String str_delete_result;

		public String doInBackground(String... params) {
			try {
				String delete_url = HttpUrl.httpStr
						+ "user/deleteaddr.action?userid=" + AdPlatform.userId
						+ "&password=" + md5.getMD5ofStr(AdPlatform.password)
						+ "&useraddrid="
						+ contactList.get(addressItem).AddressId;
				String delete_out = HttpUrl.setGetUrl(delete_url);

				// result(上传地址结果)1成功 0失败
				JSONObject delete_jsonObject = new JSONObject(delete_out);
				str_delete_result = delete_jsonObject.getString("result");
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetDeleteAddrTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_delete_result.equals("1")) {
					alertDeleteDialog(address, 0);
				} else {
					alertDeleteDialog(address, 1);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 上传地址 增加
	 * 
	 * @author user
	 * 
	 */
	private class GetUploadAddrTask extends AsyncTask<String, String, String> {
		// 上传地址
		String str_updateaddr_result;

		public String doInBackground(String... params) {
			try {
				// 设置邮寄地址(post)==========
				String updateaddr_url = HttpUrl.httpStr + "user/addaddr.action";
				String updateaddr_in = "{\"logid\":" + logId + ",\"type\":"
						+ type + ",\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"provinceid\":" + provinceId + ",\"cityid\":"
						+ cityId + ",\"address\":\"" + street
						+ "\",\"receiver\":\"" + receiver + "\",\"tel\":\""
						+ tel + "\",\"postcode\":\"" + postcode + "\"}";

				String updateaddr_out = HttpUrl.setPostUrl(updateaddr_url,
						updateaddr_in);

				// result(上传地址结果)1成功 0失败
				JSONObject updateaddr_jsonObject = new JSONObject(
						updateaddr_out);
				str_updateaddr_result = updateaddr_jsonObject
						.getString("result");
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetUploadAddrTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_updateaddr_result.equals("1")) {
					alertSendDialog(address, 4);
				} else {
					alertSendDialog(address, 1);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 上传地址 兑奖
	 * 
	 * @author user
	 * 
	 */
	private class GetExchangeAddrTask extends AsyncTask<String, String, String> {
		// 上传地址
		String str_exchangeaddr_result;

		public String doInBackground(String... params) {
			try {
				// 设置邮寄地址(post)==========
				String exchangeaddr_url = HttpUrl.httpStr
						+ "active/exchangeaddr.action?userid="
						+ AdPlatform.userId
						+ "&password="
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "&exchangelogid="
						+ logId
						+ "&useraddrid="
						+ AddressId
						+ "&md="
						+ md5.getMD5ofStr(AdPlatform.userId
								+ md5.getMD5ofStr(AdPlatform.password) + logId
								+ AddressId);
				String exchangeaddr_out = HttpUrl.setGetUrl(exchangeaddr_url);

				// result(上传地址结果)1成功 0失败
				JSONObject exchangeaddr_jsonObject = new JSONObject(
						exchangeaddr_out);
				str_exchangeaddr_result = exchangeaddr_jsonObject
						.getString("result");
			} catch (Exception e) {
				WebFailureDialog(address);
				Log.v("E-GetExchangeAddrTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_exchangeaddr_result.equals("1")) {
					alertSendDialog(address, 0);
				} else {
					alertSendDialog(address, 1);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	private void alertSendDialog(Activity activity, final int Item) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		switch (Item) {
		case 0:
			alertDialog.setTitle(getResources()
					.getString(R.string.alert_title7));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message47));
			break;
		case 1:
			alertDialog.setTitle(getResources()
					.getString(R.string.alert_title7));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message48));
			break;
		case 2:
			alertDialog.setTitle(getResources()
					.getString(R.string.alert_title8));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message49));
			break;
		case 3:
			alertDialog.setTitle(getResources()
					.getString(R.string.alert_title8));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message50));
			break;
		case 4:
			alertDialog.setTitle(getResources()
					.getString(R.string.alert_title9));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message51));
			break;
		}
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						if (Item == 0) {
							UpdateAddress.this.finish();
						}
						contactList.clear();
						address_vector.clear();
						setSendType(0);
						new GetAddressListTask().execute("");
						linearLayout.setVisibility(0);
						Add_address.setVisibility(0);
						framelayout.setVisibility(8);

					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	/**
	 * 删除结果
	 * 
	 * @param activity
	 * @param Item
	 */
	private void alertDeleteDialog(Activity activity, final int Item) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		switch (Item) {
		case 0:
			alertDialog.setTitle(getResources().getString(
					R.string.alert_title10));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message52));
			break;
		case 1:
			alertDialog.setTitle(getResources().getString(
					R.string.alert_title10));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message53));
			break;
		}
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						switch (Item) {
						case 0:
							for (int i = 0; i < address_vector.size(); i++) {
								if (address_vector.get(i).Id.equals(contactList
										.get(addressItem).AddressId)) {
									address_vector.remove(i);
								}
							}
							contactList.remove(addressItem);
							ca.notifyDataSetChanged();
							break;
						case 1:
							break;
						}
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private int Judge(String receiver, String street, String postcode,
			String tel, int province, int city) {
		int mark;
		if (receiver.equals("") || province == -1 || city == -1
				|| street.equals("") || postcode.equals("") || tel.equals("")
				|| !Common.IsUserNumber(tel)) {
			if (receiver.equals("")) {
				mark = 1;
			} else {
				if (province == -1) {
					mark = 2;
				} else {
					if (city == -1) {
						mark = 3;
					} else {
						if (street.equals("")) {
							mark = 4;
						} else {
							if (postcode.equals("")) {
								mark = 5;
							} else {
								if (tel.equals("")) {
									mark = 6;
								} else {
									if (!Common.IsUserNumber(tel)) {
										mark = 7;
									} else {
										mark = 0;
									}
								}
							}
						}
					}
				}
			}
		} else {
			mark = 0;
		}
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(
				UpdateAddress.this);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		if (mark == 0) {
			return 0;
		} else {
			switch (mark) {
			case 1:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message54));
				break;
			case 2:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message55));
				break;
			case 3:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message56));
				break;
			case 4:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message57));
				break;
			case 5:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message58));
				break;
			case 6:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message59));
				break;
			case 7:
				alertDialog.setMessage(getResources().getString(
						R.string.alert_message60));
				break;
			}
			alertDialog.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							return;
						}
					});
			alertDialog.create(); // 创建对话框
			alertDialog.show(); // 显示对话框
			return 1;
		}
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			if (back == 1) {
				linearLayout.setVisibility(0);
				Add_address.setVisibility(0);
				framelayout.setVisibility(8);
				back = 0;
				setSendType(-1);
			} else {
				super.onKeyDown(KeyEvent.KEYCODE_BACK, event);
			}
		}
		return true;
	}

	private void WebFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message11));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DIALOG_WBE: {
			ProgressDialog dialog = new ProgressDialog(this);
			dialog.setMessage(getResources()
					.getString(R.string.alert_message10));
			dialog.setIndeterminate(true);
			dialog.setCancelable(true);
			return dialog;
		}
		}
		return null;
	}

	// 是否在LIST有值
	private boolean IsInAddressList(ArrayList<Address> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).Id)) {
				return true;
			}
		}
		return false;
	}

	// 是否在LIST有值
	private boolean IsInAddressInfo(ArrayList<AddressInfo> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).AddressId)) {
				return true;
			}
		}
		return false;
	}

	public static void setLogId(String str) {
		logId = str;
	}

	public static String getType() {
		return type;
	}

	public static void setType(String str) {
		type = str;
	}

	// 已存地址列表索引
	public static int getAddressItem() {
		return addressItem;
	}

	public static void setAddressItem(int Item) {
		addressItem = Item;
	}

	// 上传类型
	public static int getSendType() {
		return SendType;
	}

	public static void setSendType(int Item) {
		SendType = Item;
	}
}