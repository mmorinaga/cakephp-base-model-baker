# BaseModelBaker plugin for CakePHP

TableとEntityの基底クラスを作成するBakeTaskのセットです。
通常のBakeでは、スキーマの変更で再度Bakeを行うと書き込んだロジックが消えてしまう為
基底クラス(全モデル共通処理の実装) > Baseクラス（bake modelの中身と同じ) > モデル名クラス（自由に編集可）
を実現するために作りました。

このプラグインを用いることでスキーマの変更に伴うBakeによるモデル上書きを回避できます。

## インストール方法

```
composer require mmorinaga/cakephp-base-model-baker
```

config/bootstrap.phpに下記を追記
```
$this->addPlugin('BaseModelBaker');
```

## つかいかた

全てのTableとEntityの基底クラスを作成
```
bin/cake bake app_model
```

通常のbake modelで吐き出されるコードを保持したBaseクラスを作成
```
bin/cake bake base_model モデル名
```

実装用モデルを作成
```
bin/cake bake extended_model モデル名
```

一旦実装に入ったら、スキーマの定義変更時に`bake base_model`で実装用モデルに影響は出さずにスキーマ定義に追随できます。

